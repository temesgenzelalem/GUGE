<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CreatorCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => CreatorResource::collection($this->collection),
        ];
    }
}
