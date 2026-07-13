<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    protected $table = 'inventory';

    protected $fillable = [
        'product_id', 'quantity', 'reserved',
        'low_stock_threshold', 'track_inventory', 'allow_backorder',
    ];

    protected $casts = [
        'track_inventory' => 'boolean',
        'allow_backorder' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function availableQuantity(): int
    {
        return max(0, $this->quantity - $this->reserved);
    }

    public function isInStock(int $requested = 1): bool
    {
        if (! $this->track_inventory) {
            return true;
        }

        if ($this->allow_backorder) {
            return true;
        }

        return $this->availableQuantity() >= $requested;
    }

    public function isLowStock(): bool
    {
        return $this->availableQuantity() <= $this->low_stock_threshold;
    }
}
