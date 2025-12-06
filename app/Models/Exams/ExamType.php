<?php

namespace App\Models\Exams;

use Illuminate\Database\Eloquent\Model;

class ExamType extends Model
{
    protected $fillable = ['name', 'code', 'scoring_strategy', 'description', 'meta'];

    protected $casts = [
        'meta' => 'array'
    ];

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }
}
