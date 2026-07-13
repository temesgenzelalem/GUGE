<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class StoryCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => StoryResource::collection($this->collection),
        ];
    }
}
