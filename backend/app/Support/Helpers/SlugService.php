<?php

namespace App\Support\Helpers;

use Illuminate\Support\Str;

class SlugService
{
    public function generate(string $value): string
    {
        return Str::slug($value);
    }
}
