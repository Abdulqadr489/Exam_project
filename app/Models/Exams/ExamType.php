<?php

namespace App\Models\Exams;

use App\Services\CommonUsage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamType extends Model
{
    use HasFactory,SoftDeletes,CommonUsage;
    protected $fillable = ['name', 'code', 'scoring_strategy', 'description', 'meta'];

    protected $casts = [
        'meta' => 'array'
    ];

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }
}
