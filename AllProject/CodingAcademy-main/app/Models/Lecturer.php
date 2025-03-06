<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecturer extends Model
{
    use HasFactory;


    protected $table = 'lecturer';

    protected $fillable = [
        'name',
        'phone',
        'notes',
        'user_add_id',
        'image',
    ];


    public function courses()
    {
        return $this->belongsToMany(Course::class, 'teach', 'lecturer_id', 'courses_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_add_id');
    }
}
