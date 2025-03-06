<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teach extends Model
{
    use HasFactory;

    protected $table = 'teach';

    protected $fillable = [
        'courses_id',
        'lecturer_id'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'courses_id');
    }

    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class, 'lecturer_id');
    }
}
