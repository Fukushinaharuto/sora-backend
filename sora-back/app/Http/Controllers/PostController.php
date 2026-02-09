<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WeatherApiService;
use App\Services\WeatherService;
use App\Models\City;
use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\PostStoreRequest;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    public function index(Request $request, $id, WeatherApiService $weatherService)
    {
        $data = $weatherService->fetchWeather($request->latitude, $request->longitude);

        if (!$data) {
            return response()->json([
                'message' => '天気情報の取得に失敗しました。',
            ], 500);
        }
        $currentWeather   = $data['wxdata'][0]['srf'][0];
        $futureWeather    = $data['wxdata'][0]['mrf'][0];

        $city_name = City::findOrFail($id)->name;
        $posts = Post::query()
            ->with(['firstImage', 'postWeatherSnapshot', 'user'])
            ->withCount('likedUsers as likes_count')
            ->withExists([
                'likedUsers as is_liked' => function ($q) {
                    if (Auth::guard('sanctum')->check()) {
                        $q->where('users.id', Auth::guard('sanctum')->id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                }
            ])
            ->where('city_id', $id);

        $categoryId = $request->query('category_id') ?? 0;
        if ($categoryId != 0) {
            $posts->where('category_id', $categoryId);
        }
        $posts = $posts->latest()->get();

        $formattedPosts = $posts->map(function ($post) {
            return [
                'id' => $post->id,
                'categoryId' => $post->category_id,
                'postedBy' => $post->user->name,
                'weatherType' => $post->postWeatherSnapshot->weather_type,
                'temperature' => $post->postWeatherSnapshot->temperature,
                'isLiked' => $post->is_liked,
                'likeCount' => $post->likes_count,
                'message' => $post->message,
                'imageUrl' => $post->firstImage?->image_url,
                'createdAt' => $post->created_at->toISOString(),
            ];
        });
        return response()->json([
            'user' => Auth::guard('sanctum')->id(),
            'cityName' => $city_name,
            'weatherType' => WeatherService::mapWeatherType($currentWeather['wx']),
            'maxTemperature' => $futureWeather['maxtemp'],
            'minTemperature' => $futureWeather['mintemp'],
            'posts' => $formattedPosts
        ]);
    }

    public function show($id)
    {
        $post = Post::with(['user', 'postImages', 'postWeatherSnapshot', 'city'])
            ->withCount('likedUsers as likes_count')
            ->withExists([
                'likedUsers as is_liked' => function ($q) {
                    if (Auth::guard('sanctum')->check()) {
                        $q->where('users.id', Auth::guard('sanctum')->id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                }
            ])
            ->findOrFail($id);
        return response()->json([
            'id' => $post->id,
            'categoryId' => $post->category_id,
            'postedBy' => $post->user->name,
            'weatherType' => $post->postWeatherSnapshot->weather_type,
            'precipitationProb' => $post->postWeatherSnapshot->precipitation,
            'windSpeed' => $post->postWeatherSnapshot->wind_speed,
            'windDirection' => WeatherService::windDirectionJapanese($post->postWeatherSnapshot->wind_direction),
            'temperature' => $post->postWeatherSnapshot->temperature,
            'isLiked' => $post->is_liked,
            'likeCount' => $post->likes_count,
            'message' => $post->message,
            'createdAt' => $post->created_at->toISOString(),
            'imagesUrl' => $post->postImages->pluck('image_url'),
            'year'   => $post->created_at->year,
            'month'  => $post->created_at->month,
            'day'    => $post->created_at->day,
            'cityName' => $post->city->name,
        ]);
    }

    public function store(PostStoreRequest $request, WeatherApiService $weatherService)
    {
        $validated = $request->validated();
         // ✅ 画像サイズ＆エラー確認ログ
    if ($request->hasFile('imageFiles')) {
        $files = $request->file('imageFiles');
        foreach ($files as $index => $file) {
            Log::info("画像 {$index} 情報", [
                'original_name' => $file->getClientOriginalName(),
                'size_bytes' => $file->getSize(),
                'size_mb' => round($file->getSize() / 1024 / 1024, 2) . 'MB',
                'error_code' => $file->getError(),
                'error_msg' => $file->getErrorMessage()
            ]);
        }
    }
        $user = Auth::user();

        $data = $weatherService->fetchWeather($validated['latitude'], $validated['longitude']);

        if (!$data) {
            return response()->json([
                'message' => '天気情報の取得に失敗しました。',
            ], 500);
        }
        $weatherNow = $data['wxdata'][0]['srf'][0];

        $uploadedPaths = [];

        try {
            DB::transaction(function () use ($validated, $user, &$uploadedPaths, $request, $weatherNow) {
                // 投稿の作成
                $post = Post::create([
                    'user_id' => $user->id,
                    'category_id' => $validated['category_id'],
                    'city_id' => $user->city_id,
                    'message' => $validated['message'],
                ]);

                // 天気スナップショットの保存
                $post->postWeatherSnapshot()->create([
                    'weather_type' => WeatherService::mapWeatherType($weatherNow['wx']),
                    'temperature' => $weatherNow['temp'],
                    'wind_speed' => $weatherNow['wndspd'],
                    'wind_direction' => $weatherNow['wnddir'],
                    'precipitation' => $weatherNow['prec'],
                ]);
                // 画像の保存処理
                if ($request->hasFile('imageFiles')) {
                    $imagesData = [];
                    foreach ($request->file('imageFiles') as $image) {
                        $path = Storage::disk('s3')->putFile('post_images', $image);

                        if (!$path) {
                            throw new \RuntimeException('画像アップロード失敗');
                        }
                        $uploadedPaths[] = $path;

                        $imagesData[] = [
                            'post_id' => $post->id,
                            'image_url' => Storage::disk('s3')->url($path),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    if (!empty($imagesData)) {
                        PostImage::insert($imagesData);
                    }
                }
            });
        } catch (\Exception $e) {
            // エラーが発生した場合、アップロードされた画像を削除
            foreach ($uploadedPaths as $path) {
                Storage::disk('s3')->delete($path);
            }

            return response()->json([
                'message' => $e->getMessage() ?: '投稿の作成に失敗しました。'
            ], 500);
        }

        return response()->json([
            'message' => '投稿が作成されました。',
        ], 201);
    }

    public function like(Request $request)
    {
        try {
            $user = Auth::user();
            $postId = $request->post_id;

            $post = Post::findOrFail($postId);

            $user->likedPosts()->toggle($postId);
            $isLiked = $user->likedPosts()->where('post_id', $postId)->exists();
            $likesCount = $post->likedUsers()->count();

            return response()->json([
                'isLiked' => $isLiked,
                'likeCount' => $likesCount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'いいねの更新に失敗しました。',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
