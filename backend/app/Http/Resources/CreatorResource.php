<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreatorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'username' => $this->username,
            'name' => $this->name,
            'slug' => $this->slug,
            'status' => $this->status,
            'region_id' => $this->region_id,
            'region' => $this->whenLoaded('region', fn () => new RegionResource($this->region)),
            'region_coverage' => $this->region?->name ?? '',
            'role' => $this->role,
            'bio' => $this->bio,
            'specialties' => $this->specialties,
            'languages' => $this->languages,
            'social_links' => $this->social_links,
            'contact_email' => $this->contact_email,
            'website_url' => $this->website_url,
            'portfolio_url' => $this->portfolio_url,
            'wiki_article' => $this->wiki_article,
            'image_url' => $this->image_url,
            'rating' => $this->rating,
            'review_count' => $this->review_count,
            'story_count' => $this->story_count,
            'product_count' => $this->product_count,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'stories' => StoryResource::collection($this->whenLoaded('stories')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
