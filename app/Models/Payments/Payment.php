<?php

namespace App\Models\Payments;

use App\Models\Exams\Exam;
use App\Models\SoftDeletes;
use App\Models\User;
use App\Models\UserExam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'user_id', 'exam_id', 'fib_payment_id',
        'fib_transaction_id', 'amount', 'status',
        'raw_request', 'raw_response', 'callback_data'
    ];

    protected $casts = [
        'raw_request'   => 'array',
        'raw_response'  => 'array',
        'callback_data' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function userExam()
    {
        return $this->hasOne(UserExam::class);
    }
}
