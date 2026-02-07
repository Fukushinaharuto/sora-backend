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
        $posts = $user->posts()->withCount('likedUsers')->get();

        $userPostCount = $posts->count();
        $likeReceivedCount = $posts->sum('liked_users_count');

        $postDates = $user->posts()->pluck('created_at')->map(fn($d) => $d->format('Y-m-d'));
        $likeDates = $user->likedPosts()
            ->pluck('likes.created_at')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'));

        $activeDates = $postDates->merge($likeDates)->unique()->sortDesc();

        $recentActivities = $activeDates
            ->take(3)
            ->map(function ($date) use ($posts) {
                $dayPosts = $posts->filter(fn($p) => $p->created_at->format('Y-m-d') === $date);
                $postCount = $dayPosts->count();
                $likeCount = $dayPosts->sum('liked_users_count');

                $carbonDate = Carbon::parse($date);
                $dateLabel = match (true) {
                    $carbonDate->isToday() => '今日',
                    $carbonDate->isYesterday() => '昨日',
                    default => $carbonDate->format('n月j日'),
                };

                return [
                    'date' => $dateLabel,
                    'postCount' => $postCount,
                    'likeCount' => $likeCount,
                ];
            })->values();

        return response()->json([
            'post_count' => $userPostCount,
            'like_count' => $likeReceivedCount,
            'activeDays' => $activeDates->count(),
            'recentActivities' => $recentActivities,
        ]);
    }
}
