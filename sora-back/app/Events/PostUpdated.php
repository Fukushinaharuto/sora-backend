<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Post;

class PostUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $post;

    /**
     * Create a new event instance.
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('posts.city.' . $this->post->city_id . '.category.' . $this->post->category_id)
        ];
    }
    public function broadcastWith()
    {
        return [
            'id' => $this->post->id,
            'category_id' => $this->post->category_id,
            'posted_by' => $this->post->user->name,
            'weather_type' => $this->post->postWeatherSnapshot->weather_type,
            'temperature' => $this->post->postWeatherSnapshot->temperature,
            'is_liked' => false,
            'like_count' => $this->post->likes_count,
            'message' => $this->post->message,
            'image_url' => $this->post->firstImage->image_url,
            'created_at' => $this->post->created_at->toISOString(),
        ];
    }
}
