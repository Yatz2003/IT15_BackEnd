<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CurrentWeatherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'city' => ['nullable', 'string', 'max:120'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lon' => ['nullable', 'numeric', 'between:-180,180'],
            'explicit_city' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $city = $this->input('city');
            $lat = $this->input('lat');
            $lon = $this->input('lon');

            // Allow empty query so controller can attempt IP-based resolution.
            if ($city === null && $lat === null && $lon === null) {
                return;
            }

            if (($lat !== null && $lon === null) || ($lat === null && $lon !== null)) {
                $validator->errors()->add('lat', 'Both lat and lon must be provided together.');
            }
        });
    }
}
