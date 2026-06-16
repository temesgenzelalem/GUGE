<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'zone'        => $this->zone,
            'direction'   => $this->direction,
            'description' => $this->description,
            'tagline'     => $this->tagline,
            'wiki_article'=> $this->wiki_article,
            'image_url'   => $this->image_url,
            'tags'        => $this->tags ?? [],
            'stats'       => $this->stats ?? [],
            'created_at'  => $this->created_at?->toISOString(),
            'updated_at'  => $this->updated_at?->toISOString(),
        ];
    }
}
