<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\City;
use App\Models\Prefecture;

class UserController extends Controller
{
    public function city(Request $request)
    {
        $city = City::where('name', $request->city_name)->firstOrFail();

        if (Auth::guard('sanctum')->check()) {
            Auth::guard('sanctum')->user()->update(['city_id' => $city->id]);
        }

        return response()->json([
            'auth_check' => Auth::guard('sanctum')->check(),
            'city_id' => $city->id,
        ]);
    }

    public function location($prefecture_name)
    {
        $prefecture = Prefecture::where('name', $prefecture_name)->firstOrFail();
        $cityNames = $prefecture->cities()->pluck('name');
        return response()->json([
            'cities' => $cityNames,
        ]);
    }

    public function me()
    {
        $user = Auth::user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'cityId' => $user->city->id,
            'cityName' => $user->city->name,
            'imageUrl' => $user->image_url,
        ]);
    }

    public function profile()
    {
        $user = Auth::user();
        $postDates = $user->posts()
            ->selectRaw('DATE(created_at) as date');

        $likeDates = $user->likedPosts()
            ->selectRaw('DATE(likes.created_at) as date');
        $activeDates = $postDates
            ->union($likeDates)
            ->distinct()
            ->pluck('date');

        $recentActivities = $activeDates
            ->sortDesc()
            ->take(3)
            ->map(function ($date) use ($user) {
                $postCount = $user->posts()
                    ->whereDate('created_at', $date)
                    ->count();
                $likeCount = $user->likedPosts()
                    ->wherePivot('created_at', $date)
                    ->count();

                return [
                    'date' => $date,
                    'postCount' => $postCount,
                    'likeCount' => $likeCount,
                ];
            });

        return response()->json([
            'post_count' => $user->post_count,
            'like_count' => $user->like_count,
            'activeDays' => $activeDates->count(),
            'recentActivities' => $recentActivities,
        ]);
    }
}
