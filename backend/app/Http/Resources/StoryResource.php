<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'slug'         => $this->slug,
            'region_id'    => $this->region_id,
            'region'       => $this->whenLoaded('region', fn () => new RegionResource($this->region)),
            'creator_id'   => $this->creator_id,
            'creator'      => $this->whenLoaded('creator', fn () => new CreatorResource($this->creator)),
            'type'         => $this->type,
            'excerpt'      => $this->excerpt,
            'body'         => $this->body,
            'wiki_article' => $this->wiki_article,
            'image_url'    => $this->image_url,
            'read_minutes' => $this->read_minutes,
            'published_at' => $this->published_at?->toISOString(),
            'created_at'   => $this->created_at?->toISOString(),
            'updated_at'   => $this->updated_at?->toISOString(),
        ];
    }
}
