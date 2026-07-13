<?php

namespace App\Http\Resources;

use App\Support\Resources\BaseCollection;

class TagCollection extends BaseCollection
{
    public $collects = TagResource::class;
}
