<?php

namespace App\Domain\Category\Events;

use App\Models\Category;
use Illuminate\Queue\SerializesModels;

class CategoryUpdated
{
    use SerializesModels;

    public function __construct(public Category $category) {}
}
