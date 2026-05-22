/**
 * Simple browser-native reactive Shopping Cart manager using localStorage
 */

export interface CartItem {
  product_id: number;
  variation_id: number | null;
  quantity: number;
  selected_attributes?: Record<string, string>;
}

const CART_KEY = "shophub_cart";

export function getCart(): CartItem[] {
  if (typeof window === "undefined") return [];
  try {
    const raw = localStorage.getItem(CART_KEY);
    return raw ? JSON.parse(raw) : [];
  } catch (e) {
    console.error("Failed to parse cart from localStorage", e);
    return [];
  }
}

export function saveCart(cart: CartItem[]): void {
  if (typeof window === "undefined") return;
  try {
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
    // Dispatch a custom event to notify all listeners (e.g. Header)
    window.dispatchEvent(new Event("cart-updated"));
  } catch (e) {
    console.error("Failed to save cart to localStorage", e);
  }
}

export function addToCart(
  productId: number,
  variationId: number | null = null,
  quantity: number = 1,
  selectedAttributes?: Record<string, string>
): void {
  const cart = getCart();
  const existingIndex = cart.findIndex(
    (item) => item.product_id === productId && item.variation_id === variationId
  );

  if (existingIndex > -1) {
    cart[existingIndex].quantity += quantity;
  } else {
    cart.push({
      product_id: productId,
      variation_id: variationId,
      quantity,
      selected_attributes: selectedAttributes,
    });
  }

  saveCart(cart);
}

export function updateCartQuantity(
  productId: number,
  variationId: number | null,
  quantity: number
): void {
  let cart = getCart();
  const existingIndex = cart.findIndex(
    (item) => item.product_id === productId && item.variation_id === variationId
  );

  if (existingIndex > -1) {
    if (quantity <= 0) {
      cart.splice(existingIndex, 1);
    } else {
      cart[existingIndex].quantity = quantity;
    }
    saveCart(cart);
  }
}

export function removeFromCart(productId: number, variationId: number | null = null): void {
  const cart = getCart();
  const filtered = cart.filter(
    (item) => !(item.product_id === productId && item.variation_id === variationId)
  );
  saveCart(filtered);
}

export function clearCart(): void {
  saveCart([]);
}

export function getCartCount(): number {
  return getCart().reduce((sum, item) => sum + item.quantity, 0);
}

export function isInCart(productId: number, variationId: number | null = null): boolean {
  const cart = getCart();
  return cart.some((item) => item.product_id === productId && item.variation_id === variationId);
}
