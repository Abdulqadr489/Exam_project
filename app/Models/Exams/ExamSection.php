<?php

namespace App\Models\Exams;

use App\Models\Questions\Question;
use App\Services\CommonUsage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamSection extends Model
{
    use HasFactory,SoftDeletes,CommonUsage;
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
