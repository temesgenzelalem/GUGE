<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'slug'         => $this->slug,
            'region_id'    => $this->region_id,
            'region'       => $this->whenLoaded('region', fn () => new RegionResource($this->region)),
            'category'     => $this->category,
            'description'  => $this->description,
            'story'        => $this->story,
            'wiki_article' => $this->wiki_article,
            'image_url'    => $this->image_url,
            'tags'         => $this->tags ?? [],
            'how_to_order' => $this->how_to_order,
            'created_at'   => $this->created_at?->toISOString(),
            'updated_at'   => $this->updated_at?->toISOString(),
        ];
    }
}
