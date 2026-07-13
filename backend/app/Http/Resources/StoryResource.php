<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'region_id' => $this->region_id,
            'region' => $this->whenLoaded('region', fn () => new RegionResource($this->region)),
            'creator_id' => $this->creator_id,
            'creator' => $this->whenLoaded('creator', fn () => new CreatorResource($this->creator)),
            'category' => $this->category,
            'type' => $this->type,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'body' => $this->body,
            'wiki_article' => $this->wiki_article,
            'featured_image' => $this->featured_image,
            'gallery' => $this->gallery ?? [],
            'status' => $this->status,
            'featured' => $this->featured,
            'read_minutes' => $this->read_minutes,
            'language' => $this->language,
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'published_at' => $this->published_at?->toISOString(),
            'view_count' => $this->view_count,
            'tags' => $this->whenLoaded('tags', fn () => $this->tags->map(fn ($tag) => ['id' => $tag->id, 'name' => $tag->name, 'slug' => $tag->slug])),
            'products' => $this->whenLoaded('products', fn () => ProductResource::collection($this->products)),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
