<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AuditLogCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => AuditLogResource::collection($this->collection),
        ];
    }
}
