<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contant',
        'description',
        'price',
        'time',
        'Reservations',
        'user_add_id',
        'image',
        'lecturer_id'
    ];

    public function subscribers()
    {
        return $this->hasMany(Get::class, 'courses_id');
    }

    public function lecturers()
    {
        return $this->belongsToMany(Lecturer::class, 'teach', 'courses_id', 'lecturer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_add_id');
    }

}
