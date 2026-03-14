<?php

namespace App\Services;

use App\Exceptions\WeatherProxyException;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class WeatherService
{
    public function dashboardWeather(array|float $location, ?float $lon = null): array
    {
        if (is_float($location) || is_int($location)) {
            if ($lon === null) {
                throw new WeatherProxyException(
                    'Longitude is required when latitude is provided.',
                    422,
                    'weather_missing_lon',
                );
            }

            $location = [
                'lat' => (float) $location,
                'lon' => (float) $lon,
            ];
        }

        $apiKey = (string) config('services.weather.key');
        $units = (string) config('services.weather.units', 'metric');

        if ($apiKey === '') {
            throw new WeatherProxyException(
                'Weather API key is not configured.',
                500,
                'weather_key_missing',
            );
        }

        $query = [
            'appid' => $apiKey,
            'units' => $units,
        ];

        if (array_key_exists('city', $location)) {
            $query['q'] = (string) $location['city'];
        } else {
            $query['lat'] = (float) $location['lat'];
            $query['lon'] = (float) $location['lon'];
        }

        $locationCacheKey = array_key_exists('q', $query)
            ? 'city:'.mb_strtolower((string) $query['q'])
            : 'geo:'.round((float) $query['lat'], 4).','.round((float) $query['lon'], 4);

        $currentPayload = $this->requestPayload(
            (string) config('services.weather.current_url'),
            $query,
            'current:'.$locationCacheKey,
        );

        $forecastPayload = $this->requestPayload(
            (string) config('services.weather.forecast_url'),
            $query,
            'forecast:'.$locationCacheKey,
        );

        $current = $this->formatCurrent($currentPayload);
        $locationName = $this->resolveLocationName($currentPayload);
        $coordinates = $this->resolveCoordinates($currentPayload, $location);

        return [
            'location' => $locationName,
            'location_name' => $locationName,
            'locationName' => $locationName,
            'coordinates' => $coordinates,
            'current' => $current,
            'forecast' => $this->formatWeeklyForecast($current, $forecastPayload),
            // Compatibility aliases for simple frontend bindings.
            'temperature' => $current['temperature'],
            'humidity' => $current['humidity'],
            'wind_speed' => $current['wind_speed'],
            'description' => $current['description'],
            'icon' => $current['icon'],
        ];
    }

    private function requestPayload(string $url, array $query, string $cacheKey): array
    {
        $ttlMinutes = max(1, (int) config('services.weather.cache_ttl_minutes', 10));

        return Cache::remember(
            'weather_api:'.$cacheKey,
            now()->addMinutes($ttlMinutes),
            function () use ($url, $query): array {
                try {
                    $response = Http::acceptJson()
                        ->timeout((int) config('services.weather.timeout', 10))
                        ->get($url, $query);
                } catch (ConnectionException $exception) {
                    throw new WeatherProxyException(
                        'Weather provider request timed out.',
                        504,
                        'weather_timeout',
                    );
                }

                $this->assertSuccessfulResponse($response);

                $payload = $response->json();

                if (! is_array($payload)) {
                    throw new WeatherProxyException(
                        'Weather provider returned an unexpected response.',
                        502,
                        'weather_provider_invalid_payload',
                    );
                }

                return $payload;
            }
        );
    }

    private function assertSuccessfulResponse(Response $response): void
    {
        if ($response->status() === 401) {
            throw new WeatherProxyException(
                'Weather provider rejected the API key.',
                502,
                'weather_provider_auth_failed',
            );
        }

        if ($response->status() === 404) {
            throw new WeatherProxyException(
                'Requested weather location was not found.',
                404,
                'weather_location_not_found',
            );
        }

        if ($response->status() === 429) {
            throw new WeatherProxyException(
                'Weather provider rate limit reached. Please retry shortly.',
                429,
                'weather_provider_rate_limited',
            );
        }

        if (! $response->successful()) {
            throw new WeatherProxyException(
                'Unable to fetch weather data right now.',
                502,
                'weather_provider_unavailable',
            );
        }
    }

    private function formatCurrent(array $payload): array
    {
        $temperatureValue = data_get($payload, 'main.temp');
        $humidity = data_get($payload, 'main.humidity');
        $windSpeed = data_get($payload, 'wind.speed');
        $description = data_get($payload, 'weather.0.description');
        $icon = data_get($payload, 'weather.0.icon');

        if (
            ! is_numeric($temperatureValue)
            || ! is_numeric($humidity)
            || ! is_numeric($windSpeed)
            || ! is_string($description)
            || ! is_string($icon)
        ) {
            throw new WeatherProxyException(
                'Weather provider returned an unexpected response.',
                502,
                'weather_provider_invalid_payload',
            );
        }

        return [
            'temperature' => $this->normalizeTemperature((float) $temperatureValue),
            'humidity' => (int) round((float) $humidity),
            'wind_speed' => round((float) $windSpeed, 1),
            'description' => mb_convert_case($description, MB_CASE_TITLE, 'UTF-8'),
            'icon' => $icon,
            'icon_url' => $this->iconUrl($icon),
        ];
    }

    private function formatWeeklyForecast(array $current, array $payload): array
    {
        $entries = data_get($payload, 'list');

        if (! is_array($entries)) {
            throw new WeatherProxyException(
                'Weather provider returned an unexpected forecast response.',
                502,
                'weather_provider_invalid_payload',
            );
        }

        $timezone = (string) config('app.timezone', 'UTC');
        $today = CarbonImmutable::now($timezone);
        $targetHours = [9, 12, 15, 18];

        $parsedEntries = collect($entries)
            ->map(function ($entry) use ($timezone) {
                $timestamp = data_get($entry, 'dt');
                $temperatureValue = data_get($entry, 'main.temp');
                $icon = data_get($entry, 'weather.0.icon');

                if (! is_numeric($timestamp) || ! is_numeric($temperatureValue) || ! is_string($icon)) {
                    return null;
                }

                $dateTime = CarbonImmutable::createFromTimestampUTC((int) $timestamp)->setTimezone($timezone);

                return [
                    'date' => $dateTime->toDateString(),
                    'day' => $dateTime->format('D'),
                    'hour' => (int) $dateTime->hour,
                    'temp' => $this->normalizeTemperature((float) $temperatureValue),
                    'icon' => $icon,
                ];
            })
            ->filter();

        $dailyForecast = $parsedEntries
            ->groupBy('date')
            ->map(function (Collection $items) use ($targetHours) {
                $day = (string) $items->first()['day'];
                $date = (string) $items->first()['date'];

                $hourly = collect($targetHours)->map(function (int $targetHour) use ($items) {
                    $closest = $items->sortBy(fn (array $item): int => abs($item['hour'] - $targetHour))->first();

                    return [
                        'time' => str_pad((string) $targetHour, 2, '0', STR_PAD_LEFT).':00',
                        'temp' => (int) $closest['temp'],
                    ];
                })->values()->all();

                $noonPoint = collect($hourly)->firstWhere('time', '12:00') ?? $hourly[0];
                $iconPoint = $items->sortBy(fn (array $item): int => abs($item['hour'] - 12))->first();

                return [
                    'day' => $day,
                    'date' => $date,
                    'temperature' => (int) $noonPoint['temp'],
                    'icon' => (string) $iconPoint['icon'],
                    'hourly' => $hourly,
                ];
            })
            ->all();

        if ($dailyForecast === []) {
            throw new WeatherProxyException(
                'Weather provider returned an empty forecast response.',
                502,
                'weather_provider_invalid_payload',
            );
        }

        $forecastDays = max(1, min(7, (int) config('services.weather.forecast_days', 5)));
        $weeklyForecast = [];

        for ($index = 0; $index < $forecastDays; $index++) {
            $date = $today->addDays($index)->toDateString();
            $dayLabel = $today->addDays($index)->format('D');
            $entry = $dailyForecast[$date] ?? null;

            if ($index === 0) {
                $todayHourly = $entry['hourly'] ?? [
                    ['time' => '09:00', 'temp' => (int) $current['temperature']],
                    ['time' => '12:00', 'temp' => (int) $current['temperature']],
                    ['time' => '15:00', 'temp' => (int) $current['temperature']],
                    ['time' => '18:00', 'temp' => (int) $current['temperature']],
                ];

                $weeklyForecast[] = [
                    'day' => $dayLabel,
                    'date' => $date,
                    'temperature' => (int) $current['temperature'],
                    'icon' => (string) $current['icon'],
                    'icon_url' => $this->iconUrl((string) $current['icon']),
                    'hourly' => $todayHourly,
                ];

                continue;
            }

            if ($entry !== null) {
                $weeklyForecast[] = [
                    'day' => $entry['day'],
                    'date' => $entry['date'],
                    'temperature' => (int) $entry['temperature'],
                    'icon' => (string) $entry['icon'],
                    'icon_url' => $this->iconUrl((string) $entry['icon']),
                    'hourly' => $entry['hourly'],
                ];

                continue;
            }

            $last = $weeklyForecast[count($weeklyForecast) - 1];

            $weeklyForecast[] = [
                'day' => $dayLabel,
                'date' => $date,
                'temperature' => (int) $last['temperature'],
                'icon' => (string) $last['icon'],
                'icon_url' => $this->iconUrl((string) $last['icon']),
                'hourly' => $last['hourly'],
            ];
        }

        return $weeklyForecast;
    }

    private function resolveLocationName(array $payload): string
    {
        $city = data_get($payload, 'name');
        $country = data_get($payload, 'sys.country');

        if (is_string($city) && is_string($country) && $country !== '') {
            return $city.', '.$country;
        }

        if (is_string($city) && $city !== '') {
            return $city;
        }

        return 'Unknown Location';
    }

    private function resolveCoordinates(array $payload, array $requestedLocation): array
    {
        $lat = data_get($payload, 'coord.lat');
        $lon = data_get($payload, 'coord.lon');

        if (is_numeric($lat) && is_numeric($lon)) {
            return [
                'lat' => (float) $lat,
                'lon' => (float) $lon,
            ];
        }

        if (array_key_exists('lat', $requestedLocation) && array_key_exists('lon', $requestedLocation)) {
            return [
                'lat' => (float) $requestedLocation['lat'],
                'lon' => (float) $requestedLocation['lon'],
            ];
        }

        return [
            'lat' => null,
            'lon' => null,
        ];
    }

    private function normalizeTemperature(float $temperatureValue): int
    {
        $units = (string) config('services.weather.units', 'metric');

        if ($units === 'standard') {
            return (int) round($temperatureValue - 273.15);
        }

        if ($units === 'imperial') {
            return (int) round((($temperatureValue - 32) * 5) / 9);
        }

        // metric is expected from config by default.
        return (int) round($temperatureValue);
    }

    private function iconUrl(string $icon): string
    {
        return 'https://openweathermap.org/img/wn/'.$icon.'@2x.png';
    }
}
