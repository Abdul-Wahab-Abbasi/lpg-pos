<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'phone', 'address', 'note', 'balance', 'total_sales', 'total_paid', 'last_visit_at'])]
class Customer extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'total_sales' => 'decimal:2',
            'total_paid' => 'decimal:2',
            'last_visit_at' => 'datetime',
        ];
    }

    public function cylinderBalances(): HasMany
    {
        return $this->hasMany(CustomerCylinderBalance::class);
    }
}
