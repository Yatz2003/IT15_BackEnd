<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $subject_name
 * @property int $program_id
 * @property int|null $prerequisite_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Program $program
 */
class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_name',
        'program_id',
        'prerequisite_id',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function prerequisite(): BelongsTo
    {
        return $this->belongsTo(self::class, 'prerequisite_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }
}
