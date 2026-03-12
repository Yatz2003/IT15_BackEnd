<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $course_name
 * @property string $department
 * @property-read int|null $students_count
 */
class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_name',
        'department',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
}
