<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MediaCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => MediaResource::collection($this->collection),
        ];
    }
}
