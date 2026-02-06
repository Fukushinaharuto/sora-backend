<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherApiService
{
    public function fetchWeather(float $lat, float $lon): ?array
    {
        $apiKey = config('services.weather.key');
        $url = 'https://wxtech.weathernews.com/api/v1/ss1wx';

        try {
            $response = Http::withHeaders([
                'X-API-Key' => $apiKey,
                'Accept' => 'application/json',
            ])->get($url, [
                'lat' => $lat,
                'lon' => $lon,
            ]);

            if ($response->successful()) {
                return $response->json();
            }
            return null;
        } catch (\Exception $e) {
            Log::error('Weather API Error: ' . $e->getMessage());
            return null;
        }
    }
}
