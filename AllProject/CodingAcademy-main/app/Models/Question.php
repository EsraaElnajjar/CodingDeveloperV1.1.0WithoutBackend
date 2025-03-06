<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'answer',
        'user_add_id',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_add_id');
    }
}
