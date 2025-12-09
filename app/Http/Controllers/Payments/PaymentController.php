<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Exams\Exam;
use App\Models\Payments\Payment;
use Illuminate\Http\Request;
use Rawahamid\FibIntegration\Payments\FibPayment;

class PaymentController extends Controller
{
    public function createPayment(Request $request, Exam $exam)
    {
        $amount = $exam->price;

        $fib = FibPayment::create($amount);

        $payment = Payment::create([
            'user_id' => auth()->id(),
            'exam_id' => $exam->id,
            'amount' => $amount,
            'fib_payment_id' => $fib['paymentId'],
            'status' => 'UNPAID',
            'raw_response' => $fib
        ]);

        return [
            'payment' => $payment,
            'fib' => $fib
        ];
    }

    public function fibCallback(Request $request)
    {
        $payment = Payment::where('fib_payment_id', $request->paymentId)->first();

        if ($payment) {
            $payment->update([
                'status' => $request->status == "PAID" ? "PAID" : "FAILED",
                'callback_data' => $request->all()
            ]);
        }

        return response()->json(["ok" => true]);
    }
}
