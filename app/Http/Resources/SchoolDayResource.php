<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolDayResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date?->toDateString(),
            'attendance_rate' => (float) $this->attendance_rate,
            'is_holiday' => $this->is_holiday,
            'created_at' => $this->created_at,
        ];
    }
}
