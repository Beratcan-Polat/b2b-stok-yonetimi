<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartService
{
    private const SESSION_KEY = 'cart';

    public function __construct(private Request $request)
    {
    }

    /**
     *
     * @return array<int, int>
     */
    private function guestCart(): array
    {
        return $this->request->session()->get(self::SESSION_KEY, []);
    }

    /**
     * @param array<int, int> $cart
     */
    private function storeGuestCart(array $cart): void
    {
        if ($cart === []) {
            $this->request->session()->forget(self::SESSION_KEY);

            return;
        }

        $this->request->session()->put(self::SESSION_KEY, $cart);
    }

    /**
     *
     * @return Collection<int, array{
     *     product_id: int,
     *     product: Product|null,
     *     quantity: int
     * }>
     */
    public function items(): Collection
    {
        $user = $this->request->user();

        if ($user !== null) {
            $quantities = $user->cartItems()
                ->pluck('quantity', 'product_id')
                ->all();
        } else {
            $quantities = $this->guestCart();
        }

        $products = Product::withTrashed()
            ->whereIn('id', array_keys($quantities))
            ->get()
            ->keyBy('id');

        return collect($quantities)
            ->map(function ($quantity, $productId) use ($products): array {
                return [
                    'product_id' => (int) $productId,
                    'product' => $products->get((int) $productId),
                    'quantity' => (int) $quantity,
                ];
            })
            ->values();
    }

    public function add(Product $product, int $quantity): void
    {
        // Eklemede mevcut adedin üzerine ekle.
        $this->changeQuantity($product, $quantity, true);
    }

    public function update(Product $product, int $quantity): void
    {
        $this->changeQuantity($product, $quantity, false);
    }

    private function changeQuantity(
        Product $product,
        int $quantity,
        bool $increase,
    ): void {
        $user = $this->request->user();

        if ($user === null) {
            $cart = $this->guestCart();

            if (! $increase && ! array_key_exists($product->id, $cart)) {
                abort(404);
            }

            $newQuantity = $increase
                ? ($cart[$product->id] ?? 0) + $quantity
                : $quantity;

            $this->assertAvailableQuantity($product, $newQuantity);

            $cart[$product->id] = $newQuantity;

            $this->storeGuestCart($cart);

            return;
        }

        $this->withUserCartLock(
            $user,
            function () use ($user, $product, $quantity, $increase): void {
                $item = $user->cartItems()
                    ->where('product_id', $product->id)
                    ->first();

                if (! $increase && $item === null) {
                    abort(404);
                }

                $newQuantity = $increase
                    ? ($item?->quantity ?? 0) + $quantity
                    : $quantity;

                $this->assertAvailableQuantity($product, $newQuantity);

                $user->cartItems()->updateOrCreate(
                    ['product_id' => $product->id],
                    ['quantity' => $newQuantity],
                );
            },
        );
    }

    private function assertAvailableQuantity(
        Product $product,
        int $quantity,
    ): void {

        if (
            $product->trashed()
            || $quantity < 1
            || $quantity > (int) $product->stock
        ) {
            throw ValidationException::withMessages([
                'quantity' => __('sepet.stock-error'),
            ]);
        }
    }

    public function remove(int $productId): void
    {
        $user = $this->request->user();

        if ($user === null) {
            $cart = $this->guestCart();

            unset($cart[$productId]);

            $this->storeGuestCart($cart);

            return;
        }

        $this->withUserCartLock(
            $user,
            function () use ($user, $productId): void {
                // Başka kullanıcının satırına erişilmez.
                $user->cartItems()
                    ->where('product_id', $productId)
                    ->delete();
            },
        );
    }

    public function clear(): void
    {
        $user = $this->request->user();

        if ($user === null) {
            $this->request->session()->forget(self::SESSION_KEY);

            return;
        }

        $this->withUserCartLock($user, function () use ($user): void {
            $user->cartItems()->delete();
        });
    }


    public function mergeGuestCart(): bool
    {
        $cart = $this->guestCart();

        if ($cart === []) {
            $this->request->session()->forget(self::SESSION_KEY);

            return false;
        }

        $user = $this->request->user();

        abort_unless($user instanceof User, 403);

        $needsReview = $this->withUserCartLock(
            $user,
            function () use ($user, $cart): bool {
                $products = Product::withTrashed()
                    ->whereIn('id', array_keys($cart))
                    ->get()
                    ->keyBy('id');

                $needsReview = false;

                foreach ($cart as $productId => $quantity) {
                    $product = $products->get((int) $productId);

                    if ($product === null) {
                        $needsReview = true;

                        continue;
                    }

                    $item = $user->cartItems()->firstOrNew([
                        'product_id' => (int) $productId,
                    ]);

                    $item->quantity = ($item->quantity ?? 0) + (int) $quantity;
                    $item->save();

                    if (
                        $product->trashed()
                        || $item->quantity > (int) $product->stock
                    ) {
                        $needsReview = true;
                    }
                }

                return $needsReview;
            },
        );

        $this->request->session()->forget(self::SESSION_KEY);

        return $needsReview;
    }


    private function withUserCartLock(
        User $user,
        Closure $operation,
    ): mixed {
        return DB::transaction(function () use ($user, $operation): mixed {
            User::query()
                ->whereKey($user->id)
                ->lockForUpdate()
                ->firstOrFail();

            return $operation();
        }, 3);
    }
}