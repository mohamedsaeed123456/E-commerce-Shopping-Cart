<?php

namespace App\Services\Order;

use App\Exceptions\CartEmptyException;
use App\Exceptions\InsufficientStockException;
use App\Jobs\NotifyLowStock;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Process checkout and create order
     *
     * @throws CartEmptyException
     * @throws InsufficientStockException
     */
    public function checkout(int $userId): Order
    {
        $cartItems = CartItem::where('user_id', $userId)
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            throw new CartEmptyException();
        }

        return DB::transaction(function () use ($cartItems, $userId) {
            $total = 0;
            $orderItems = [];

            // Calculate total and prepare order items
            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;

                // Validate stock availability
                if ($cartItem->quantity > $product->stock_quantity) {
                    throw new InsufficientStockException(
                        "Insufficient stock for {$product->name}. Available: {$product->stock_quantity}",
                        $product->stock_quantity,
                        $product->name
                    );
                }

                $itemTotal = $product->price * $cartItem->quantity;
                $total += $itemTotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $cartItem->quantity,
                    'price' => $product->price,
                ];

                // Reduce stock
                $product->decrement('stock_quantity', $cartItem->quantity);
            }

            // Create order
            $order = Order::create([
                'user_id' => $userId,
                'total' => $total,
            ]);

            // Create order items
            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            // Clear cart
            CartItem::where('user_id', $userId)->delete();

            return $order->load('orderItems.product');
        });
    }
}
