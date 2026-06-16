<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreatorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'slug'            => $this->slug,
            'role'            => $this->role,
            'bio'             => $this->bio,
            'region_coverage' => $this->region_coverage,
            'wiki_article'    => $this->wiki_article,
            'image_url'       => $this->image_url,
            'contact_email'   => $this->contact_email,
            'stories'         => StoryResource::collection($this->whenLoaded('stories')),
            'created_at'      => $this->created_at?->toISOString(),
            'updated_at'      => $this->updated_at?->toISOString(),
        ];
    }
}
