<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'price', 'stock_quantity', 'low_stock_notified_at'];

    /**
     * Get the low stock threshold from config
     */
    public static function getLowStockThreshold(): int
    {
        return (int) config('mail.low_stock_threshold', 10);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= self::getLowStockThreshold();
    }
}
