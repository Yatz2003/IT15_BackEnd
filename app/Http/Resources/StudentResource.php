<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'age' => $this->age,
            'gender' => $this->gender,
            'program_id' => $this->program_id ?? $this->course_id,
            'program' => new CourseResource($this->whenLoaded('program')),
            'course_id' => $this->course_id,
            'course' => new CourseResource($this->whenLoaded('course')),
            'created_at' => $this->created_at,
        ];
    }
}
