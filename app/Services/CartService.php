<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Session;

class CartService
{
    private const CART_SESSION_KEY = 'cart';

    public function getCart(): array
    {
        return Session::get(self::CART_SESSION_KEY, []);
    }

    public function putCart(array $cart): void
    {
        $this->storeCart($cart);
    }

    public function clearCart(): void
    {
        Session::forget(self::CART_SESSION_KEY);
    }

    public function getItem(string|int $key): ?array
    {
        $cart = $this->getCart();

        return $cart[$key] ?? null;
    }

    public function hasItem(string|int $key): bool
    {
        return $this->getItem($key) !== null;
    }

    public function generateCartKey(int $productId, ?int $variationId = null, array $selectedAttributes = []): string
    {
        $cartKey = (string) $productId;

        if ($variationId) {
            return $cartKey.'-'.$variationId;
        }

        if (! empty($selectedAttributes)) {
            ksort($selectedAttributes);
            return $cartKey.'-'.md5(json_encode($selectedAttributes));
        }

        return $cartKey;
    }

    public function addOrIncrementBasicProduct(Product $product, int $quantity = 1): void
    {
        $this->addOrUpdateBasicProduct(
            product: $product,
            quantity: $quantity,
            replaceQuantity: false,
        );
    }

    public function addOrSetBasicProduct(Product $product, int $quantity): void
    {
        $this->addOrUpdateBasicProduct(
            product: $product,
            quantity: $quantity,
            replaceQuantity: true,
        );
    }

    public function addOrIncrementItem(string|int $key, array $item, int $quantity = 1): void
    {
        $cart = $this->getCart();

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $quantity;
            $cart[$key]['price'] = $item['price'];
            $cart[$key]['currency_symbol'] = $item['currency_symbol'] ?? ($cart[$key]['currency_symbol'] ?? null);
            $cart[$key]['thumbnail'] = $item['thumbnail'] ?? ($cart[$key]['thumbnail'] ?? null);
            $cart[$key]['attributes'] = $item['attributes'] ?? ($cart[$key]['attributes'] ?? []);
        } else {
            $item['quantity'] = $quantity;
            $cart[$key] = $item;
        }

        $this->storeCart($cart);
    }

    private function addOrUpdateBasicProduct(Product $product, int $quantity, bool $replaceQuantity): void
    {
        $key = (string) $product->id;
        $cart = $this->getCart();
        $salePrice = app(\App\Services\FlashSaleService::class)->salePriceForProduct($product);
        $resolvedPrice = $salePrice !== null ? $salePrice : (float) $product->price;

        if (isset($cart[$key])) {
            if ($replaceQuantity) {
                $cart[$key]['quantity'] = $quantity;
            } else {
                $cart[$key]['quantity'] += $quantity;
            }
            $cart[$key]['price'] = $resolvedPrice;
            $cart[$key]['currency_symbol'] = $product->currency_symbol;
            $cart[$key]['thumbnail'] = $product->thumbnail;
        } else {
            $cart[$key] = [
                'name' => $product->name,
                'quantity' => $quantity,
                'price' => $resolvedPrice,
                'currency_symbol' => $product->currency_symbol,
                'thumbnail' => $product->thumbnail,
            ];
        }

        $this->storeCart($cart);
    }

    private function storeCart(array $cart): void
    {
        Session::put(self::CART_SESSION_KEY, $cart);
    }
}
