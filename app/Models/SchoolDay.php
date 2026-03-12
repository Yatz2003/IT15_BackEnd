<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'attendance_rate',
        'is_holiday',
    ];

    protected $casts = [
        'date' => 'date',
        'attendance_rate' => 'decimal:2',
        'is_holiday' => 'boolean',
    ];
}
