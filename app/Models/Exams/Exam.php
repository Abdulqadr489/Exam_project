<?php

namespace App\Models\Exams;

use App\Models\Payments\Payment;
use App\Models\UserExam;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = [
        'exam_type_id', 'title', 'description', 'duration', 'price_iqd'
    ];

    public function type()
    {
        return $this->belongsTo(ExamType::class, 'exam_type_id');
    }

    public function sections()
    {
        return $this->hasMany(ExamSection::class);
    }

    public function userExams()
    {
        return $this->hasMany(UserExam::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
