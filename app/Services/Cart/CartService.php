<?php

namespace App\Services\Cart;

use App\Exceptions\InsufficientStockException;
use App\Models\CartItem;
use App\Models\Product;

class CartService
{
    /**
     * Get all cart items for a user
     */
    public function getUserCartItems(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return CartItem::where('user_id', $userId)
            ->with('product')
            ->get();
    }

    /**
     * Add product to cart
     *
     * @throws InsufficientStockException
     */
    public function addToCart(int $userId, int $productId, int $quantity): CartItem
    {
        $product = Product::findOrFail($productId);

        // Check if item already exists in cart
        $cartItem = CartItem::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        $newQuantity = $cartItem
            ? $cartItem->quantity + $quantity
            : $quantity;

        // Validate stock availability
        if ($newQuantity > $product->stock_quantity) {
            throw new InsufficientStockException(
                "Not enough stock available. Available: {$product->stock_quantity}",
                $product->stock_quantity,
                $product->name
            );
        }

        // Update or create cart item
        if ($cartItem) {
            $cartItem->update(['quantity' => $newQuantity]);
            return $cartItem->fresh(['product']);
        }

        return CartItem::create([
            'user_id' => $userId,
            'product_id' => $productId,
            'quantity' => $quantity,
        ])->load('product');
    }

    /**
     * Update cart item quantity
     *
     * @throws InsufficientStockException
     */
    public function updateCartItem(CartItem $cartItem, int $quantity): CartItem
    {
        // Validate ownership
        if ($cartItem->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Validate stock availability
        if ($quantity > $cartItem->product->stock_quantity) {
            throw new InsufficientStockException(
                "Not enough stock available. Available: {$cartItem->product->stock_quantity}",
                $cartItem->product->stock_quantity,
                $cartItem->product->name
            );
        }

        $cartItem->update(['quantity' => $quantity]);

        return $cartItem->fresh(['product']);
    }

    /**
     * Remove item from cart
     */
    public function removeCartItem(CartItem $cartItem): bool
    {
        // Validate ownership
        if ($cartItem->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        return $cartItem->delete();
    }
}
