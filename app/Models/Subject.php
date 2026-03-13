<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $code
 * @property string $subject_name
 * @property int|null $program_id
 * @property string $department
 * @property int $units
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Course|null $program
 */
class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'subject_name',
        'program_id',
        'department',
        'units',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'program_id');
    }
}
