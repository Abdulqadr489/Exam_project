<?php

namespace App\Models;

use App\Models\Exams\Exam;
use App\Models\Payments\Payment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserExam extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'user_id', 'exam_id', 'payment_id',
        'started_at', 'completed_at',
        'final_score', 'score_breakdown'
    ];

    protected $casts = [
        'score_breakdown' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function answers()
    {
        return $this->hasMany(UserAnswer::class);
    }
}
