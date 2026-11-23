<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CartItemRequest;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class CartController extends Controller
{
    public function __construct(private CartService $cartService)
    {

    }

    public function index(Request $request): View
    {
        $items = $this->cartService->items();

        $total = $items->sum(function (array $item): float {
            return (float) ($item['product']?->price ?? 0) * $item['quantity'];
        });

        $hasPendingGuestCart = $request->user() !== null && $request->session()->get('cart', []) !== [];

        return view('katalog.sepet', compact(
            'items',
            'total',
            'hasPendingGuestCart',
        ));
    }

    public function store(CartItemRequest $request, Product $urun): RedirectResponse {
        $this->cartService->add(
            $urun,
            $request->validated('quantity')
        );

        return back()->with('success', __('sepet.added'));
    }

    public function update(CartItemRequest $request, Product $urun): RedirectResponse {
        $this->cartService->update(
            $urun,
            $request->validated('quantity')
        );

        return to_route('sepet.index')->with('success', __('sepet.updated'));
    }

    public function destroy(int $urun): RedirectResponse {
        $this->cartService->remove($urun);

        return to_route('sepet.index')->with('success', __('sepet.removed'));
    }

    public function clear(): RedirectResponse {
        $this->cartService->clear();

        return to_route('sepet.index')->with('success', __('sepet.cleared'));
    }

    public function account(): RedirectResponse {
        return to_route('sepet.index');
    }

    public function merge(): RedirectResponse {
        try {
            $needsReview = $this->cartService->mergeGuestCart();
        } catch (Throwable $exception) {
            report($exception);

            return to_route('sepet.index')->with('error', __('sepet.merge-failed'));
        }

        return to_route('sepet.index')->with(
            $needsReview ? 'error' : 'success',
            $needsReview ? __('sepet.review') : __('sepet.merged')
        );
    }
    
}
