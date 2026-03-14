<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\WeatherProxyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CurrentWeatherRequest;
use App\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Throwable;

class WeatherController extends Controller
{
    public function __construct(private readonly WeatherService $weatherService)
    {
    }

    public function show(CurrentWeatherRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $location = $this->resolveLocation($request, $validated);

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

    public function mini(CurrentWeatherRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $location = $this->resolveMiniLocation($request, $validated);

        try {
            $weather = $this->weatherService->dashboardWeather($location);

            return response()->json([
                'location' => $weather['location'] ?? null,
                'coordinates' => $weather['coordinates'] ?? null,
                'temperature' => $weather['temperature'] ?? null,
                'description' => $weather['description'] ?? null,
                'icon' => $weather['icon'] ?? null,
                'icon_url' => data_get($weather, 'current.icon_url'),
                'source' => 'live',
            ]);
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

    private function resolveLocation(Request $request, array $validated): array
    {
        if (array_key_exists('city', $validated) && is_string($validated['city']) && trim($validated['city']) !== '') {
            return ['city' => trim($validated['city'])];
        }

        if (array_key_exists('lat', $validated) && array_key_exists('lon', $validated)) {
            return [
                'lat' => (float) $validated['lat'],
                'lon' => (float) $validated['lon'],
            ];
        }

        $ipLocation = $this->resolveLocationFromIp($request->ip());
        if ($ipLocation !== null) {
            return $ipLocation;
        }

        throw new WeatherProxyException(
            'Location is required. Send device latitude/longitude or city.',
            422,
            'weather_location_required',
        );
    }

    private function resolveMiniLocation(Request $request, array $validated): array
    {
        if (array_key_exists('lat', $validated) && array_key_exists('lon', $validated)) {
            return [
                'lat' => (float) $validated['lat'],
                'lon' => (float) $validated['lon'],
            ];
        }

        if (
            $request->boolean('explicit_city', false)
            && array_key_exists('city', $validated)
            && is_string($validated['city'])
            && trim($validated['city']) !== ''
        ) {
            return [
                'city' => trim($validated['city']),
            ];
        }

        throw new WeatherProxyException(
            'Mini weather requires device coordinates. Send lat and lon from browser geolocation.',
            422,
            'mini_weather_location_required',
        );
    }

    private function resolveLocationFromIp(?string $ip): ?array
    {
        if (! is_string($ip) || trim($ip) === '' || $ip === '127.0.0.1' || $ip === '::1') {
            return null;
        }

        try {
            $response = Http::acceptJson()
                ->timeout(4)
                ->get('http://ip-api.com/json/'.$ip, [
                    'fields' => 'status,lat,lon',
                ]);

            if (! $response->successful()) {
                return null;
            }

            $payload = $response->json();
            $status = data_get($payload, 'status');
            $lat = data_get($payload, 'lat');
            $lon = data_get($payload, 'lon');

            if ($status !== 'success' || ! is_numeric($lat) || ! is_numeric($lon)) {
                return null;
            }

            return [
                'lat' => (float) $lat,
                'lon' => (float) $lon,
            ];
        } catch (Throwable) {
            return null;
        }
    }
}
