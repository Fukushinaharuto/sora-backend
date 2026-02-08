<?php

namespace App\Services;

class WeatherService
{
    public static function mapWeatherType(int $wxCode): string
    {
        return match ($wxCode) {
            100, 500, 550         => 'clear',
            200, 600              => 'cloudy',
            300, 430, 650, 850    => 'rain',
            400, 950              => 'snow',
            default               => 'clear',
        };
    }

    public static function windDirectionJapanese(int $dir): string
    {
        return match ($dir) {
            1  => '北北東',
            2  => '北東',
            3  => '東北東',
            4  => '東',
            5  => '東南東',
            6  => '南東',
            7  => '南南東',
            8  => '南',
            9  => '南南西',
            10 => '南西',
            11 => '西南西',
            12 => '西',
            13 => '西北西',
            14 => '北西',
            15 => '北北西',
            16 => '北',
            default => '不明',
        };
    }
}
