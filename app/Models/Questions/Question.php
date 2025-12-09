<?php

namespace App\Models\Questions;

use App\Models\Exams\ExamSection;
use App\Models\Options\Option;
use App\Models\UserAnswer;
use App\Services\CommonUsage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory,SoftDeletes,CommonUsage;
    protected $fillable = [
        'exam_section_id', 'question_type', 'question_text',
        'media_url', 'max_score', 'meta'
    ];

    protected $casts = [
        'meta' => 'array'
    ];

    public function section()
    {
        return $this->belongsTo(ExamSection::class, 'exam_section_id');
    }

    public function options()
    {
        return $this->hasMany(Option::class);
    }

    public function answers()
    {
        return $this->hasMany(UserAnswer::class);
    }
}
