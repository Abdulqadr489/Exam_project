<?php

namespace App\Models\Exams;

use App\Models\Questions\Question;
use Illuminate\Database\Eloquent\Model;

class ExamSection extends Model
{
    protected $fillable = ['exam_id', 'name', 'order', 'instructions'];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
