<?php

namespace App\Http\Controllers\Cart;

use App\Exceptions\CartEmptyException;
use App\Exceptions\InsufficientStockException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\UpdateCartRequest;
use App\Http\Responses\ApiResponse;
use App\Models\CartItem;
use App\Services\Cart\CartService;
use App\Services\Order\OrderService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private OrderService $orderService
    ) {}

    public function index(): Response
    {
        $cartItems = $this->cartService->getUserCartItems(auth()->id());

        return Inertia::render('Cart/Index', [
            'cartItems' => $cartItems,
        ]);
    }

    public function add(AddToCartRequest $request): RedirectResponse
    {
        try {
            $cartItem = $this->cartService->addToCart(
                auth()->id(),
                $request->product_id,
                $request->quantity
            );

            return ApiResponse::redirectSuccess('cart.index', 'Product added to cart!');
        } catch (InsufficientStockException $e) {
            return ApiResponse::backError($e->getMessage(), ['quantity' => $e->getMessage()]);
        }
    }

    public function update(UpdateCartRequest $request, CartItem $cartItem): RedirectResponse
    {
        try {
            $this->cartService->updateCartItem($cartItem, $request->quantity);

            return ApiResponse::redirectSuccess('cart.index', 'Cart updated!');
        } catch (InsufficientStockException $e) {
            return ApiResponse::backError($e->getMessage(), ['quantity' => $e->getMessage()]);
        }
    }

    public function remove(CartItem $cartItem): RedirectResponse
    {
        $this->cartService->removeCartItem($cartItem);

        return ApiResponse::redirectSuccess('cart.index', 'Item removed from cart!');
    }

    public function checkout(): RedirectResponse
    {
        try {
            $order = $this->orderService->checkout(auth()->id());

            return ApiResponse::redirectSuccess('cart.index', 'Order placed successfully!');
        } catch (CartEmptyException $e) {
            return ApiResponse::redirectError('cart.index', $e->getMessage());
        } catch (InsufficientStockException $e) {
            return ApiResponse::redirectError('cart.index', $e->getMessage());
        } catch (\Exception $e) {
            return ApiResponse::redirectError('cart.index', 'An error occurred. Please try again.');
        }
    }
}
