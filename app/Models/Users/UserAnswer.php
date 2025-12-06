<?php

namespace App\Models;

use App\Models\Questions\Question;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAnswer extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'user_exam_id', 'question_id',
        'selected_option_ids', 'user_text_answer',
        'score_obtained', 'explanation'
    ];

    protected $casts = [
        'selected_option_ids' => 'array',
        'user_text_answer' => 'array'
    ];

    public function userExam()
    {
        return $this->belongsTo(UserExam::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
