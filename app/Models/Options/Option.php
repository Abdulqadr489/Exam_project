<?php

namespace App\Models\Options;

use App\Models\Questions\Question;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Option extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'question_id', 'option_text', 'media_url',
        'is_correct', 'weight', 'explanation'
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
