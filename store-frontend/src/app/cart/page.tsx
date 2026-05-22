"use client";

import React, { useEffect, useState } from "react";
import Header from "../../components/Header";
import Navbar from "../../components/Navbar";
import Footer from "../../components/Footer";
import { api, ResolvedCartItem } from "../../utils/api";
import { getCart, updateCartQuantity, removeFromCart, getCartCount } from "../../utils/cart";

export default function CartPage() {
  const [resolvedItems, setResolvedItems] = useState<ResolvedCartItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  const loadCartDetails = async () => {
    const localItems = getCart();
    if (localItems.length === 0) {
      setResolvedItems([]);
      setLoading(false);
      return;
    }

    try {
      // Map local storage items to the resolution payload expected by Laravel
      const payload = localItems.map((item) => ({
        product_id: item.product_id,
        variation_id: item.variation_id,
        quantity: item.quantity,
      }));

      const res = await api.resolveCart(payload);
      setResolvedItems(res);
    } catch (err: any) {
      console.error("Cart resolution failure", err);
      setError("Failed to validate items with active store prices. Please reload.");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadCartDetails();
    
    // Refresh if cart changes locally
    const handleCartUpdated = () => {
      loadCartDetails();
    };
    window.addEventListener("cart-updated", handleCartUpdated);
    return () => window.removeEventListener("cart-updated", handleCartUpdated);
  }, []);

  const handleQtyChange = (item: ResolvedCartItem, newQty: number) => {
    const stockLimit = item.stock;
    const finalQty = Math.max(0, Math.min(stockLimit, newQty));
    
    updateCartQuantity(item.product_id, item.variation_id, finalQty);
  };

  const handleRemove = (item: ResolvedCartItem) => {
    removeFromCart(item.product_id, item.variation_id);
  };

  // Calculations
  const subtotal = resolvedItems.reduce((sum, item) => sum + item.price * item.quantity, 0);
  const currencySymbol = resolvedItems[0]?.currency_symbol || "$";
  const originalSubtotal = resolvedItems.reduce((sum, item) => sum + (item.original_price || item.price) * item.quantity, 0);
  const totalSavings = originalSubtotal - subtotal;

  return (
    <div className="min-h-screen flex flex-col bg-[#f4f7fb]">
      <Header />
      <Navbar />

      <main className="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-6 pb-12">
        <h1 className="text-xl md:text-2xl font-black text-gray-900 uppercase tracking-tight mb-6">Shopping Cart</h1>

        {loading ? (
          <div className="space-y-4 animate-pulse">
            <div className="h-20 bg-slate-200 rounded-2xl w-full"></div>
            <div className="h-20 bg-slate-200 rounded-2xl w-full"></div>
            <div className="h-20 bg-slate-200 rounded-2xl w-3/4"></div>
          </div>
        ) : error ? (
          <div className="bg-rose-50 border border-rose-100 rounded-3xl p-8 text-center max-w-md mx-auto shadow-sm">
            <span className="text-2xl">⚠️</span>
            <p className="text-xs text-rose-600 font-bold mt-2">{error}</p>
            <button
              onClick={loadCartDetails}
              className="mt-4 px-4 py-2 bg-rose-600 text-white font-bold text-xs uppercase tracking-wider rounded-xl hover:bg-rose-700 transition-all active:scale-95 shadow-sm shadow-rose-500/10"
            >
              Retry Validation
            </button>
          </div>
        ) : resolvedItems.length === 0 ? (
          <div className="bg-white rounded-3xl border border-gray-150 p-12 text-center max-w-xl mx-auto shadow-sm animate-in fade-in duration-300">
            <div className="h-16 w-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 font-black text-2xl">
              🛒
            </div>
            <h3 className="text-md font-bold text-gray-950 mb-1.5 uppercase tracking-wide">Your Cart is Empty</h3>
            <p className="text-xs text-gray-500 mb-5 leading-relaxed">
              Looks like you haven't added any products to your cart yet. Let's head over to the shop catalogue and browse some items!
            </p>
            <a
              href="/search"
              className="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs uppercase tracking-wider shadow-md shadow-blue-500/10 transition-all active:scale-95 inline-block"
            >
              Browse Products
            </a>
          </div>
        ) : (
          <div className="grid grid-cols-1 lg:grid-cols-[1fr_350px] gap-8 animate-in fade-in duration-300">
            
            {/* LEFT: Items List */}
            <div className="space-y-4">
              {resolvedItems.map((item, idx) => {
                const hasSaving = item.original_price && item.original_price > item.price;
                return (
                  <div
                    key={`${item.product_id}-${item.variation_id || "base"}`}
                    className="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm flex flex-col sm:flex-row items-center gap-4 relative group"
                  >
                    
                    {/* Thumbnail */}
                    <div className="h-16 w-16 rounded-xl bg-gray-50 border border-gray-50 overflow-hidden flex-shrink-0">
                      {item.thumbnail_url ? (
                        <img src={item.thumbnail_url} alt={item.name} className="w-full h-full object-cover" />
                      ) : (
                        <div className="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400 font-bold text-[8px] uppercase">No Image</div>
                      )}
                    </div>

                    {/* Meta information */}
                    <div className="flex-grow text-center sm:text-left space-y-1">
                      <h3 className="font-bold text-gray-900 text-xs sm:text-sm hover:text-blue-600 transition-colors cursor-pointer" onClick={() => window.location.href = `/product/${item.slug}/`}>
                        {item.name}
                      </h3>
                      {item.variation_name && (
                        <p className="text-[10px] text-gray-400 font-bold uppercase tracking-wide">
                          Configuration: {item.variation_name}
                        </p>
                      )}
                      
                      {!item.in_stock && (
                        <span className="inline-block rounded-full bg-rose-50 px-2 py-0.5 text-[8px] font-black uppercase tracking-wider text-rose-600">
                          Unavailable (Out of stock)
                        </span>
                      )}
                    </div>

                    {/* Quantity controls */}
                    <div className="flex items-center border border-gray-150 rounded-xl bg-gray-50 h-8 px-1 flex-shrink-0">
                      <button
                        onClick={() => handleQtyChange(item, item.quantity - 1)}
                        className="h-6 w-6 flex items-center justify-center font-black text-gray-500 hover:text-gray-900 hover:bg-white rounded-md transition-all"
                      >
                        -
                      </button>
                      <span className="w-8 text-center text-xs font-black text-gray-800">{item.quantity}</span>
                      <button
                        disabled={item.quantity >= item.stock}
                        onClick={() => handleQtyChange(item, item.quantity + 1)}
                        className="h-6 w-6 flex items-center justify-center font-black text-gray-500 hover:text-gray-900 hover:bg-white rounded-md transition-all disabled:opacity-30"
                      >
                        +
                      </button>
                    </div>

                    {/* Price and delete */}
                    <div className="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center gap-4 sm:gap-1.5 min-w-[100px] w-full sm:w-auto pt-3 sm:pt-0 border-t sm:border-t-0 border-gray-50">
                      <div className="flex flex-col sm:text-right">
                        {hasSaving && (
                          <span className="text-[9px] font-bold text-gray-400 line-through leading-none mb-1">
                            {currencySymbol}
                            {(item.original_price * item.quantity).toFixed(2)}
                          </span>
                        )}
                        <span className="font-extrabold text-blue-600 text-sm leading-none">
                          {currencySymbol}
                          {(item.price * item.quantity).toFixed(2)}
                        </span>
                      </div>

                      <button
                        onClick={() => handleRemove(item)}
                        className="rounded-full p-1.5 text-gray-400 hover:bg-rose-50 hover:text-rose-500 transition-all active:scale-90"
                        title="Remove Item"
                      >
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" strokeWidth="2.5" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                      </button>
                    </div>

                  </div>
                );
              })}
            </div>

            {/* RIGHT: Order Summary */}
            <div className="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm space-y-5 h-fit">
              <h3 className="text-xs font-black uppercase tracking-wider text-gray-900 border-b border-gray-100 pb-2">Order Summary</h3>
              
              <div className="space-y-2 text-xs">
                <div className="flex justify-between items-center text-gray-500 font-medium">
                  <span>Subtotal ({getCartCount()} items)</span>
                  <span className="font-bold text-gray-800">
                    {currencySymbol}
                    {(originalSubtotal).toFixed(2)}
                  </span>
                </div>
                {totalSavings > 0 && (
                  <div className="flex justify-between items-center text-emerald-600 font-bold">
                    <span>Discount Savings</span>
                    <span>
                      -{currencySymbol}
                      {totalSavings.toFixed(2)}
                    </span>
                  </div>
                )}
                <div className="flex justify-between items-center text-gray-500 font-medium pb-2">
                  <span>Shipping Cost</span>
                  <span className="text-[10px] font-bold text-gray-400 uppercase">Calculated at checkout</span>
                </div>
                <div className="flex justify-between items-center text-sm font-black text-gray-950 border-t border-gray-100 pt-3">
                  <span>Estimated Total</span>
                  <span className="text-base text-blue-600">
                    {currencySymbol}
                    {subtotal.toFixed(2)}
                  </span>
                </div>
              </div>

              <a
                href="/checkout"
                className="w-full h-11 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs uppercase tracking-wider flex items-center justify-center transition-all active:scale-95 shadow-md shadow-blue-500/10 hover:shadow-blue-500/20"
              >
                Proceed to Checkout
              </a>
              <a
                href="/search"
                className="w-full h-11 rounded-xl border border-gray-250 bg-white hover:bg-gray-50 text-gray-700 font-bold text-xs uppercase tracking-wider flex items-center justify-center transition-all active:scale-95"
              >
                Continue Shopping
              </a>
            </div>

          </div>
        )}
      </main>

      <Footer />
    </div>
  );
}
