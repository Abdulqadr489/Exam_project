<?php

namespace App\Models\Plans;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'name','description','price','duration_days'
    ];

    public function users() {
        return $this->belongsToMany(User::class, 'user_plans')
            ->withPivot(['starts_at','expires_at','is_active'])
            ->withTimestamps();
    }
}
