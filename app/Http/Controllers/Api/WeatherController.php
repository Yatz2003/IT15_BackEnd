<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\WeatherProxyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CurrentWeatherRequest;
use App\Services\WeatherService;
use Illuminate\Http\JsonResponse;
use Throwable;

class WeatherController extends Controller
{
    public function __construct(private readonly WeatherService $weatherService)
    {
    }

    public function show(CurrentWeatherRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $location = [];

        if (array_key_exists('city', $validated) && is_string($validated['city']) && trim($validated['city']) !== '') {
            $location['city'] = trim($validated['city']);
        } else {
            $location['lat'] = array_key_exists('lat', $validated)
                ? (float) $validated['lat']
                : (float) config('services.weather.default_lat', 14.5995);
            $location['lon'] = array_key_exists('lon', $validated)
                ? (float) $validated['lon']
                : (float) config('services.weather.default_lon', 120.9842);
        }

        try {
            $weather = $this->weatherService->dashboardWeather($location);

            return response()->json($weather);
        } catch (WeatherProxyException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'error_code' => $exception->errorCode(),
            ], $exception->statusCode());
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Unexpected weather proxy error.',
                'error_code' => 'weather_proxy_error',
            ], 500);
        }
    }
}
