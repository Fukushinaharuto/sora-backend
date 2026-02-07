<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\City;
use App\Models\Post;
use App\Models\Prefecture;
use Carbon\Carbon;

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
        $postStats = $user->posts()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as post_count')
            ->withCount('likedUsers')
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $userPostCount = $postStats->sum('post_count');
        $likeReceivedCount = $postStats->sum('liked_users_count');

        $postDates = $user->posts()
            ->selectRaw('DATE(created_at) as date');

        $likeDates = $user->likedPosts()
            ->selectRaw('DATE(likes.created_at) as date');

        $activeDates = $postDates
            ->union($likeDates)
            ->pluck('date');

        $recentActivities = $activeDates
            ->sortDesc()
            ->take(3)
            ->map(function ($date) use ($postStats) {
                $stats = $postStats->get($date);

                $carbonDate = Carbon::parse($date);
                if ($carbonDate->isToday()) {
                    $dateLabel = '今日';
                } elseif ($carbonDate->isYesterday()) {
                    $dateLabel = '昨日';
                } else {
                    $dateLabel = $carbonDate->format('n月j日');
                }

                return [
                    'date' => $dateLabel,
                    'postCount' => $stats->post_count,
                    'likeCount' => $stats->liked_users_count,
                ];
            });

        return response()->json([
            'post_count' => $userPostCount,
            'like_count' => $likeReceivedCount,
            'activeDays' => $activeDates->count(),
            'recentActivities' => $recentActivities,
        ]);
    }
}
