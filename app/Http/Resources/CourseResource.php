<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'course_name' => $this->course_name,
            'department' => $this->department,
            'student_count' => $this->whenCounted('students'),
            'created_at' => $this->created_at,
        ];
    }
}
