"use client";

import React, { useState, useEffect, useRef } from "react";
import Link from "next/link";
import { addToCart, removeFromCart, isInCart } from "../utils/cart";

interface SaleProduct {
  id: number;
  name: string;
  slug: string;
  price: number;
  originalPrice: number;
  discountPercent: number;
  currencySymbol: string;
  thumbnail?: string;
}

interface FlashSaleProps {
  title?: string;
  subtitle?: string;
  endsAtISO?: string;
  products?: SaleProduct[];
}

const defaultProducts: SaleProduct[] = [
  {
    id: 201,
    name: "Logitech MX Keys S Keyboard",
    slug: "logitech-mx-keys-s-keyboard",
    price: 89.0,
    originalPrice: 119.0,
    discountPercent: 25,
    currencySymbol: "$",
    thumbnail: "https://images.unsplash.com/photo-1595225476474-87563907a212?w=500&q=80",
  },
  {
    id: 202,
    name: "Sony SRS-XB13 Portable Speaker",
    slug: "sony-srs-xb13-portable-speaker",
    price: 38.0,
    originalPrice: 59.0,
    discountPercent: 35,
    currencySymbol: "$",
    thumbnail: "https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=500&q=80",
  },
  {
    id: 203,
    name: "Anker USB-C 100W Charging Dock",
    slug: "anker-usb-c-100w-charging-dock",
    price: 45.0,
    originalPrice: 79.0,
    discountPercent: 43,
    currencySymbol: "$",
    thumbnail: "https://images.unsplash.com/photo-1583863788434-e58a36330cf0?w=500&q=80",
  },
];

export default function FlashSale({
  title = "Super Flash Sale",
  subtitle = "Hurry up! Grab limited-time deals on hot items.",
  endsAtISO,
  products = defaultProducts,
}: FlashSaleProps) {
  const containerRef = useRef<HTMLDivElement>(null);
  const scrollAmount = 300;
  const [countdown, setCountdown] = useState("00:00:00");
  const [cartState, setCartState] = useState<{ [id: number]: boolean }>({});

  // 4 hours from now as a default countdown
  const targetTime = endsAtISO
    ? new Date(endsAtISO).getTime()
    : new Date().getTime() + 4 * 60 * 60 * 1000;

  useEffect(() => {
    const tick = () => {
      const now = new Date().getTime();
      const diff = targetTime - now;

      if (diff <= 0) {
        setCountdown("ENDED");
        return;
      }

      const totalSeconds = Math.floor(diff / 1000);
      const hours = Math.floor(totalSeconds / 3600);
      const minutes = Math.floor((totalSeconds % 3600) / 60);
      const seconds = totalSeconds % 60;

      const pad = (num: number) => String(num).padStart(2, "0");
      setCountdown(`${pad(hours)}:${pad(minutes)}:${pad(seconds)}`);
    };

    tick();
    const interval = setInterval(tick, 1000);
    return () => clearInterval(interval);
  }, [targetTime]);

  useEffect(() => {
    const updateCart = () => {
      const state: { [id: number]: boolean } = {};
      products.forEach((p) => {
        state[p.id] = isInCart(p.id);
      });
      setCartState(state);
    };
    updateCart();
    window.addEventListener("cart-updated", updateCart);
    return () => window.removeEventListener("cart-updated", updateCart);
  }, [products]);

  const scrollLeft = () => {
    containerRef.current?.scrollBy({ left: -scrollAmount, behavior: "smooth" });
  };

  const scrollRight = () => {
    containerRef.current?.scrollBy({ left: scrollAmount, behavior: "smooth" });
  };

  const toggleCart = (id: number, e: React.MouseEvent) => {
    e.stopPropagation();
    e.preventDefault();
    if (isInCart(id)) {
      removeFromCart(id);
    } else {
      addToCart(id, null, 1);
    }
  };

  return (
    <section className="mb-10 rounded-3xl border border-rose-100 bg-gradient-to-br from-rose-50/50 via-white to-orange-50/20 p-5 md:p-6 shadow-sm shadow-rose-500/5 relative group">
      <div className="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div className="space-y-2">
          {/* Flash sale badge */}
          <div className="inline-flex items-center gap-1.5 rounded-full border border-rose-200 bg-rose-50 px-3 py-1 text-[9px] font-black uppercase tracking-[0.2em] text-rose-600 shadow-sm shadow-rose-500/5">
            <svg
              className="h-3 w-3 text-rose-500 animate-pulse"
              fill="currentColor"
              viewBox="0 0 24 24"
              aria-hidden="true"
            >
              <path d="M13.5 2.25 4.5 14.25H11l-1.5 7.5 9-12H12l1.5-7.5Z" />
            </svg>
            <span>Flash Sale</span>
          </div>

          <div className="flex flex-col gap-2.5 sm:flex-row sm:items-center sm:gap-4">
            <h2 className="text-xl font-extrabold tracking-tight text-gray-900 md:text-2xl">
              {title}
            </h2>
            {countdown && (
              <div className="inline-flex items-center gap-2 rounded-full border border-rose-100 bg-white px-3.5 py-1.5 shadow-sm">
                <span className="text-[9px] font-bold uppercase tracking-[0.16em] text-gray-400">
                  Ends In
                </span>
                <span className="text-xs font-black text-rose-600 font-mono tracking-wider">
                  {countdown}
                </span>
              </div>
            )}
          </div>
          {subtitle && <p className="text-xs text-gray-500 max-w-2xl">{subtitle}</p>}
        </div>

        <div className="flex items-start gap-2">
          <Link
            href="/flash-sale"
            className="inline-flex items-center rounded-full border border-rose-200 bg-white px-4 py-2 text-xs font-bold text-rose-600 shadow-sm transition hover:bg-rose-600 hover:text-white"
          >
            View All
          </Link>
        </div>
      </div>

      <div className="relative mt-6">
        {/* Left Arrow */}
        <button
          onClick={scrollLeft}
          className="absolute left-[-16px] top-1/2 -translate-y-1/2 z-20 bg-white/95 backdrop-blur-sm shadow-md rounded-full p-2.5 text-rose-500 hover:scale-110 active:scale-95 transition-all duration-300 opacity-0 group-hover:opacity-100 hidden md:flex items-center justify-center border border-rose-100"
          aria-label="Scroll Left"
        >
          <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M15 19l-7-7 7-7" />
          </svg>
        </button>

        {/* Products Scroll Container */}
        <div
          ref={containerRef}
          className="flex gap-4 overflow-x-auto scroll-smooth snap-x snap-mandatory pb-2 no-scrollbar"
        >
          {products.map((product) => {
            const isInCart = !!cartState[product.id];
            return (
              <div
                key={product.id}
                className="flex-none w-[200px] sm:w-[240px] overflow-hidden rounded-2xl border border-gray-100 bg-white p-3 hover:shadow-md transition-all duration-300 cursor-pointer snap-start flex flex-col justify-between"
                onClick={() => (window.location.href = `/product/${product.slug}/`)}
              >
                <div>
                  <div className="relative aspect-square overflow-hidden bg-gray-50 rounded-xl mb-3 border border-gray-50">
                    {product.thumbnail ? (
                      <img
                        src={product.thumbnail}
                        alt={product.name}
                        className="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                      />
                    ) : (
                      <div className="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400 font-bold text-xs">
                        No Image
                      </div>
                    )}
                    <div className="absolute left-2.5 top-2.5 inline-flex items-center gap-1 rounded-full bg-rose-500 px-2 py-0.5 text-[8px] font-black uppercase tracking-wide text-white shadow-sm shadow-rose-500/15">
                      <svg className="h-2.5 w-2.5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M13.5 2.25 4.5 14.25H11l-1.5 7.5 9-12H12l1.5-7.5Z" />
                      </svg>
                      <span>-{product.discountPercent}%</span>
                    </div>
                  </div>

                  <div className="">
                    <h3 className="text-xs sm:text-sm font-bold text-gray-900 transition-colors group-hover:text-rose-600 leading-snug line-clamp-2">
                      {product.name}
                    </h3>
                  </div>
                </div>

                <div className="pt-2">
                  <div className="flex items-end justify-between gap-2 mt-2">
                    <div className="flex flex-col">
                      <span className="text-[10px] font-semibold text-gray-400 line-through leading-none mb-1">
                        {product.currencySymbol}
                        {product.originalPrice.toFixed(2)}
                      </span>
                      <span className="text-sm font-black text-rose-600 leading-none">
                        {product.currencySymbol}
                        {product.price.toFixed(2)}
                      </span>
                    </div>

                    <button
                      onClick={(e) => toggleCart(product.id, e)}
                      className={`text-[10px] font-bold px-3 py-2 rounded-xl transition-all active:scale-95 flex items-center gap-1 shadow-sm border ${
                        isInCart
                          ? "bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100"
                          : "bg-white text-gray-700 border-gray-200 hover:border-rose-500 hover:text-rose-600"
                      }`}
                      title={isInCart ? "Remove from cart" : "Add to Cart"}
                    >
                      {isInCart ? (
                        <>
                          <svg
                            className="w-3.5 h-3.5"
                            fill="none"
                            stroke="currentColor"
                            strokeWidth="2.5"
                            viewBox="0 0 24 24"
                          >
                            <path strokeLinecap="round" strokeLinejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                          </svg>
                          <span>Added</span>
                        </>
                      ) : (
                        <>
                          <svg
                            className="w-3.5 h-3.5"
                            fill="none"
                            stroke="currentColor"
                            strokeWidth="2.2"
                            viewBox="0 0 24 24"
                          >
                            <path
                              strokeLinecap="round"
                              strokeLinejoin="round"
                              d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.116 60.116 0 0 0-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"
                            />
                          </svg>
                          <span>Add</span>
                        </>
                      )}
                    </button>
                  </div>
                  <p className="mt-1.5 text-[10px] font-bold text-emerald-600 leading-none">
                    Save {product.originalPrice - product.price} {product.currencySymbol}
                  </p>
                </div>
              </div>
            );
          })}
        </div>

        {/* Right Arrow */}
        <button
          onClick={scrollRight}
          className="absolute right-[-16px] top-1/2 -translate-y-1/2 z-20 bg-white/95 backdrop-blur-sm shadow-md rounded-full p-2.5 text-rose-500 hover:scale-110 active:scale-95 transition-all duration-300 opacity-0 group-hover:opacity-100 hidden md:flex items-center justify-center border border-rose-100"
          aria-label="Scroll Right"
        >
          <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>
    </section>
  );
}
