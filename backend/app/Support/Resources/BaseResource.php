<?php

namespace App\Support\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

abstract class BaseResource extends JsonResource
{
    public function toArray($request): array
    {
        return parent::toArray($request);
    }
}
