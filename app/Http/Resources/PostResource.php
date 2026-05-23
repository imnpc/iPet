<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'avatar' => $this->user->avatar,
            ]),
            'pet_id' => $this->pet_id,
            'pet' => $this->whenLoaded('pet', fn () => [
                'id' => $this->pet->id,
                'name' => $this->pet->name,
            ]),
            'content' => $this->content,
            'location' => $this->location,
            'visibility' => $this->visibility,
            'like_count' => $this->like_count,
            'comment_count' => $this->comment_count,
            'view_count' => $this->view_count,
            'share_count' => $this->share_count,
            'is_pinned' => $this->is_pinned,
            'allow_comment' => $this->allow_comment,
            'published_at' => $this->published_at,
            'media' => $this->whenLoaded('media', fn () => $this->media->map(fn ($m) => [
                'id' => $m->id,
                'type' => $m->type,
                'url' => $m->url(),
                'thumbnail_url' => $m->thumbnailUrl(),
                'width' => $m->width,
                'height' => $m->height,
                'duration' => $m->duration,
                'sort_order' => $m->sort_order,
            ])),
            'tags' => $this->whenLoaded('tags', fn () => $this->tags->map(fn ($t) => [
                'id' => $t->id,
                'name' => $t->name,
                'slug' => $t->slug,
            ])),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
