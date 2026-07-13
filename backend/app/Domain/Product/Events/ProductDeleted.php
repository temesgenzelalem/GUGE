<?php

namespace App\Domain\Product\Events;

use App\Models\Product;
use Illuminate\Queue\SerializesModels;

class ProductDeleted
{
    use SerializesModels;

    public function __construct(public Product $product) {}
}
