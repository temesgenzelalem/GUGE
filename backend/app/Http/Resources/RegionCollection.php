<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RegionCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => RegionResource::collection($this->collection),
        ];
    }
}
