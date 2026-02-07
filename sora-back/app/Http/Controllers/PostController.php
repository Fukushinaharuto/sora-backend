<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WeatherApiService;
use App\Models\City;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\PostWeatherSnapshot;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\PostStoreRequest;

class PostController extends Controller
{
    public function index(Request $request, $id)
    {
        $city_name = City::findOrFail($id)->name;
        $posts = Post::query()
            ->with(['firstImage', 'postWeatherSnapshot', 'user'])
            ->withCount('likedUsers as likes_count')
            ->withExists([
                'likedUsers as is_liked' => function ($q) {
                    $q->where('users.id', Auth::id());
                }
            ])
            ->where('city_id', $id);
        if ($request->filled('category_id')) {
            $posts->where('category_id', $request->category_id);
        }
        $posts = $posts->latest()->get();

        $formattedPosts = $posts->map(function ($post) {
            return [
                'id' => $post->id,
                'category_id' => $post->category_id,
                'posted_by' => $post->user->name,
                'weather_type' => $post->postWeatherSnapshot->weather_type,
                'temperature' => $post->postWeatherSnapshot->temperature,
                'is_liked' => $post->is_liked,
                'like_count' => $post->likes_count,
                'message' => $post->message,
                'image_url' => $post->firstImage->image_url,
                'created_at' => $post->created_at->toISOString(),
            ];
        });
        return response()->json([
            'city_name' => $city_name,
            'posts' => $formattedPosts
        ]);
    }

    public function show($id)
    {
        $post = Post::with(['user', 'postImages', 'postWeatherSnapshot', 'city'])
            ->withCount('likedUsers as likes_count')
            ->withExists([
                'likedUsers as is_liked' => function ($q) {
                    $q->where('users.id', Auth::id());
                }
            ])
            ->findOrFail($id);
        return response()->json([
            'id' => $post->id,
            'category_id' => $post->category_id,
            'posted_by' => $post->user->name,
            'weather_type' => $post->postWeatherSnapshot->weather_type,
            'precipitation' => $post->postWeatherSnapshot->precipitation,
            'wind_speed' => $post->postWeatherSnapshot->wind_speed,
            'wind_direction' => PostWeatherSnapshot::windDirectionJapanese($post->postWeatherSnapshot->wind_direction),
            'temperature' => $post->postWeatherSnapshot->temperature,
            'is_liked' => $post->is_liked,
            'like_count' => $post->likes_count,
            'message' => $post->message,
            'created_at' => $post->created_at->toISOString(),
            'image_urls' => $post->postImages->pluck('image_url'),
            'year'   => $post->created_at->year,
            'month'  => $post->created_at->month,
            'day'    => $post->created_at->day,
            'city_name' => $post->city->name,
        ]);
    }

    public function store(PostStoreRequest $request, WeatherApiService $weatherService)
    {
        $validated = $request->validated();
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
                    'weather_type' => PostWeatherSnapshot::mapWeatherType($weatherNow['wx']),
                    'temperature' => $weatherNow['temp'],
                    'wind_speed' => $weatherNow['wndspd'],
                    'wind_direction' => $weatherNow['wnddir'],
                    'precipitation' => $weatherNow['prec'],
                ]);

                $path = Storage::disk('s3')->putFile('post_images', $request->file('imageFiles'));
                $url = Storage::disk('s3')->url($path);
                $post->postImages()->create([
                    'image_url' => $url,
                ]);
                // 画像の保存処理
                $imagesData = [];
                foreach ($request->file('imageFiles') as $image) {
                    $path = Storage::disk('s3')->putFile('post_images', $image);
                    $uploadedPaths[] = $path;

                    $url = Storage::disk('s3')->url($path);
                    $imagesData[] = [
                        'post_id' => $post->id,
                        'image_url' => $url,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                PostImage::insert($imagesData);

                $post = Post::with(['user', 'firstImage', 'postWeatherSnapshot'])
                    ->withCount('likedUsers')
                    ->find($post->id);
            });
        } catch (\Exception $e) {
            // エラーが発生した場合、アップロードされた画像を削除
            foreach ($uploadedPaths as $path) {
                Storage::disk('s3')->delete($path);
            }
            Log::error('エラー発生', ['exception' => $e]);

            return response()->json([
                'message' => '投稿の作成に失敗しました。'
            ], 500);
        }

        return response()->json([
            'message' => '投稿が作成されました。',
        ], 201);
    }

    public function like(Request $request)
    {
        $user = Auth::user();
        $postId = $request->post_id;

        $post = Post::findOrFail($postId);
        // いいね済みなら解除、未いいねなら追加
        $user->likedPosts()->toggle($postId);
        $post->loadCount('likedUsers');

        return response()->json([
            'message' => 'いいね状態が更新されました。',
        ]);
    }
}
