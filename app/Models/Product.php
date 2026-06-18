<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'category', 'sale_price', 'refill_charge', 'return_deposit', 'unit', 'qty', 'min_qty', 'max_qty'])]
class Product extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'sale_price' => 'decimal:2',
            'refill_charge' => 'decimal:2',
            'return_deposit' => 'decimal:2',
        ];
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Low stock: current qty under 25% of max capacity.
     */
    protected function isLowStock(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->max_qty > 0 && ($this->qty / $this->max_qty) < 0.25,
        );
    }

    protected function stockPercent(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->max_qty > 0 ? min(100, (int) round($this->qty / $this->max_qty * 100)) : 0,
        );
    }
}
