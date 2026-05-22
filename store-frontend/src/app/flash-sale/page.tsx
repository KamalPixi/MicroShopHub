"use client";

import React, { useEffect, useState } from "react";
import Header from "../../components/Header";
import Navbar from "../../components/Navbar";
import Footer from "../../components/Footer";
import { api, ProductData } from "../../utils/api";
import { addToCart, isInCart } from "../../utils/cart";

export default function FlashSalePage() {
  const [flashSale, setFlashSale] = useState<any>(null);
  const [products, setProducts] = useState<ProductData[]>([]);
  const [loading, setLoading] = useState(true);
  const [countdown, setCountdown] = useState("00:00:00");
  const [cartState, setCartState] = useState<{ [id: number]: boolean }>({});

  // Sync cart item states
  useEffect(() => {
    const updateCartStates = () => {
      const state: { [id: number]: boolean } = {};
      products.forEach((p) => {
        state[p.id] = isInCart(p.id);
      });
      setCartState(state);
    };
    updateCartStates();
    window.addEventListener("cart-updated", updateCartStates);
    return () => window.removeEventListener("cart-updated", updateCartStates);
  }, [products]);

  // Load Active Flash Sale
  useEffect(() => {
    api.fetchFlashSale()
      .then((res) => {
        if (res && res.flash_sale) {
          setFlashSale(res.flash_sale);
          setProducts(res.products || []);
        }
      })
      .catch((err) => {
        console.error("Failed to load flash sale dynamic catalog", err);
      })
      .finally(() => {
        setLoading(false);
      });
  }, []);

  // Tick Countdown Timer
  useEffect(() => {
    if (!flashSale || !flashSale.ends_at) return;

    const target = new Date(flashSale.ends_at).getTime();
    
    const tick = () => {
      const now = new Date().getTime();
      const diff = target - now;

      if (diff <= 0) {
        setCountdown("ENDED");
        return;
      }

      const totalSeconds = Math.floor(diff / 1000);
      const hours = Math.floor(totalSeconds / 3600);
      const minutes = Math.floor((totalSeconds % 3600) / 60);
      const seconds = totalSeconds % 60;

      const pad = (n: number) => String(n).padStart(2, "0");
      setCountdown(`${pad(hours)}:${pad(minutes)}:${pad(seconds)}`);
    };

    tick();
    const interval = setInterval(tick, 1000);
    return () => clearInterval(interval);
  }, [flashSale]);

  return (
    <div className="min-h-screen flex flex-col bg-[#f4f7fb]">
      <Header />
      <Navbar />

      <main className="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-6 pb-12">
        
        {loading ? (
          <div className="space-y-8 animate-pulse">
            <div className="h-44 w-full bg-slate-200 rounded-3xl"></div>
            <div className="h-6 w-48 bg-slate-200 rounded"></div>
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
              {[1, 2, 3, 4].map((i) => (
                <div key={i} className="aspect-[4/5] bg-slate-100 rounded-2xl border border-slate-200/50"></div>
              ))}
            </div>
          </div>
        ) : flashSale ? (
          <div className="space-y-8 animate-in fade-in duration-300">
            
            {/* Countdown Banner Hub */}
            <div className="bg-gradient-to-br from-rose-500 via-rose-600 to-indigo-700 rounded-3xl p-6 md:p-10 text-white shadow-xl relative overflow-hidden text-center md:text-left md:flex items-center justify-between gap-8">
              <div className="absolute inset-0 opacity-10 pointer-events-none">
                <div className="absolute -top-12 -left-12 h-64 w-64 rounded-full bg-white blur-2xl"></div>
                <div className="absolute -bottom-20 -right-20 h-80 w-80 rounded-full bg-yellow-400 blur-3xl opacity-30"></div>
              </div>

              <div className="relative z-10 space-y-3 max-w-xl">
                <div className="inline-flex items-center gap-1.5 rounded-full border border-white/20 bg-white/10 px-3.5 py-1 text-[9px] font-black uppercase tracking-[0.2em] text-white shadow-inner">
                  ⚡ Dynamic Flash Campaign
                </div>
                <h1 className="text-xl md:text-3xl font-black uppercase tracking-tight leading-tight">
                  {flashSale.title}
                </h1>
                <p className="text-xs text-white/80 leading-relaxed max-w-md">
                  {flashSale.subtitle || flashSale.description || "Incredible, limited-time massive price reductions on top-rated items!"}
                </p>
              </div>

              <div className="relative z-10 mt-6 md:mt-0 flex-shrink-0 bg-white text-gray-900 px-6 py-4 rounded-2xl shadow-lg border border-rose-100 flex flex-col items-center justify-center min-w-[200px] mx-auto md:mx-0">
                <span className="text-[10px] font-black text-rose-500 uppercase tracking-widest leading-none mb-2">Campaign Ends In</span>
                <span className="text-2xl font-black text-slate-900 font-mono tracking-wider">{countdown}</span>
              </div>
            </div>

            {/* Catalog Grid */}
            <section className="space-y-4">
              <h2 className="text-md font-black uppercase tracking-wider text-gray-950 pl-1">Deals Catalog ({products.length})</h2>
              
              {products.length > 0 ? (
                <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                  {products.map((prod) => {
                    const hasSale = prod.sale_price !== null;
                    const activePrice = hasSale ? (prod.sale_price as number) : prod.price;
                    const originalPrice = prod.price;
                    const discountPercent = prod.discount_percentage || Math.round(((originalPrice - activePrice) / originalPrice) * 100);
                    const isAdded = !!cartState[prod.id];

                    return (
                      <div
                        key={prod.id}
                        onClick={() => (window.location.href = `/product/${prod.slug}/`)}
                        className="bg-white rounded-2xl border border-gray-100 p-3 hover:shadow-md transition-all duration-300 relative cursor-pointer group flex flex-col justify-between"
                      >
                        <div>
                          {/* Image */}
                          <div className="aspect-square bg-gray-50 border border-gray-50 rounded-xl overflow-hidden mb-2.5 relative">
                            {prod.thumbnail_url ? (
                              <img src={prod.thumbnail_url} alt={prod.name} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                            ) : (
                              <div className="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400 font-black text-[9px] uppercase">No Image</div>
                            )}

                            <div className="absolute left-2 top-2 inline-flex items-center gap-0.5 rounded-full bg-rose-500 px-2 py-0.5 text-[8px] font-black uppercase tracking-wider text-white shadow-sm shadow-rose-500/15">
                              ⚡ -{discountPercent}%
                            </div>
                          </div>

                          {/* Title */}
                          <h3 className="text-xs sm:text-sm font-bold text-gray-900 line-clamp-2 leading-snug group-hover:text-rose-600 transition-colors">
                            {prod.name}
                          </h3>
                        </div>

                        {/* Price & Cart Add */}
                        <div className="flex items-end justify-between gap-2 pt-3">
                          <div className="flex flex-col">
                            <span className="text-[10px] font-semibold text-gray-400 line-through leading-none mb-1">
                              {prod.currency_symbol || "$"}
                              {originalPrice.toFixed(2)}
                            </span>
                            <span className="font-extrabold text-rose-600 text-sm leading-none">
                              {prod.currency_symbol || "$"}
                              {activePrice.toFixed(2)}
                            </span>
                          </div>

                          <button
                            onClick={(e) => {
                              e.stopPropagation();
                              e.preventDefault();
                              if (isAdded) return;
                              addToCart(prod.id, null, 1);
                            }}
                            className={`text-[10px] font-bold px-2.5 py-2 rounded-xl transition-all active:scale-95 flex items-center gap-1 shadow-sm border ${
                              isAdded
                                ? "bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100"
                                : "bg-white text-gray-700 border-gray-200 hover:border-rose-500 hover:text-rose-600"
                            }`}
                          >
                            {isAdded ? "Added" : "Add"}
                          </button>
                        </div>
                      </div>
                    );
                  })}
                </div>
              ) : (
                <div className="bg-white rounded-3xl border border-gray-150 p-12 text-center max-w-xl mx-auto shadow-sm">
                  <p className="text-xs text-gray-400">All flash sale products are currently sold out. Stay tuned for our next restock campaign!</p>
                </div>
              )}
            </section>
          </div>
        ) : (
          <div className="bg-white rounded-3xl border border-gray-150 p-12 text-center max-w-xl mx-auto shadow-sm animate-in fade-in duration-300">
            <div className="h-16 w-16 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-4 font-black text-2xl">
              ⚡
            </div>
            <h3 className="text-md font-bold text-gray-900 mb-1.5 uppercase tracking-wide">No Active Flash Campaign</h3>
            <p className="text-xs text-gray-500 mb-5 max-w-md mx-auto leading-relaxed">
              There is currently no active flash sale running right now. Follow our newsletter to receive live campaign announcements!
            </p>
            <a
              href="/"
              className="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs uppercase tracking-wider shadow-md shadow-blue-500/10 transition-all active:scale-95 inline-block"
            >
              Return Home
            </a>
          </div>
        )}
      </main>

      <Footer />
    </div>
  );
}
