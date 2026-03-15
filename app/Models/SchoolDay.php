<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $date
 * @property string|null $day_name
 * @property bool $is_holiday
 * @property string|null $event_name
 * @property int $attendance_rate
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class SchoolDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'day_name',
        'is_holiday',
        'event_name',
        'attendance_rate',
    ];

    protected $casts = [
        'date' => 'date',
        'attendance_rate' => 'integer',
        'is_holiday' => 'boolean',
    ];
}
