"use client";

import React, { useRef, useState, useEffect } from "react";
import Link from "next/link";
import { addToCart, removeFromCart, isInCart } from "../utils/cart";

interface Product {
  id: number;
  name: string;
  slug: string;
  price: number;
  currencySymbol: string;
  thumbnail?: string;
}

interface NewArrivalsProps {
  title?: string;
  products?: Product[];
}

const defaultProducts: Product[] = [
  {
    id: 301,
    name: "Minimalist Leather Desk Mat",
    slug: "minimalist-leather-desk-mat",
    price: 49.0,
    currencySymbol: "$",
    thumbnail: "https://images.unsplash.com/photo-1585776245991-cf89dd7fc73a?w=500&q=80",
  },
  {
    id: 302,
    name: "Portable LED Video Projector",
    slug: "portable-led-video-projector",
    price: 189.0,
    currencySymbol: "$",
    thumbnail: "https://images.unsplash.com/photo-1535016120720-40c646be5580?w=500&q=80",
  },
  {
    id: 303,
    name: "Stainless Steel Smart Water Bottle",
    slug: "stainless-steel-smart-water-bottle",
    price: 35.0,
    currencySymbol: "$",
    thumbnail: "https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=500&q=80",
  },
  {
    id: 304,
    name: "Concrete Desktop Pen Holder",
    slug: "concrete-desktop-pen-holder",
    price: 24.0,
    currencySymbol: "$",
    thumbnail: "https://images.unsplash.com/photo-1513542789411-b6a5d4f31634?w=500&q=80",
  },
];

export default function NewArrivals({
  title = "New Arrivals",
  products = defaultProducts,
}: NewArrivalsProps) {
  const containerRef = useRef<HTMLDivElement>(null);
  const scrollAmount = 300;
  const [cartState, setCartState] = useState<{ [id: number]: boolean }>({});

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
    <section className="mb-10 relative group">
      <div className="flex items-center justify-between mb-4 px-1">
        <h2 className="text-xl font-extrabold text-gray-900 tracking-tight">{title}</h2>
        <Link
          href="/search?sort=newest"
          className="text-xs font-bold text-blue-600 hover:text-blue-700 transition-colors uppercase tracking-wider"
        >
          View All →
        </Link>
      </div>

      <div className="relative">
        {/* Left Arrow */}
        <button
          onClick={scrollLeft}
          className="absolute left-[-16px] top-1/2 -translate-y-1/2 z-20 bg-white/95 backdrop-blur-sm shadow-md rounded-full p-2.5 text-gray-600 hover:text-blue-600 hover:scale-110 active:scale-95 transition-all duration-300 opacity-0 group-hover:opacity-100 hidden md:flex items-center justify-center border border-gray-150"
          aria-label="Scroll Left"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            strokeWidth="3"
            stroke="currentColor"
            className="w-4 h-4"
          >
            <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
          </svg>
        </button>

        {/* Scroll Container */}
        <div
          ref={containerRef}
          className="flex gap-4 overflow-x-auto scroll-smooth snap-x snap-mandatory pb-2 no-scrollbar"
        >
          {products.length > 0 ? (
            products.map((product) => {
              const isInCart = !!cartState[product.id];
              return (
                <div
                  key={product.id}
                  className="flex-none w-[200px] sm:w-[240px] snap-start bg-white rounded-2xl border border-gray-100 p-3 hover:shadow-md transition-all duration-300 group/card relative flex flex-col justify-between cursor-pointer"
                  onClick={() => (window.location.href = `/product/${product.slug}/`)}
                >
                  <div>
                    {/* Image */}
                    <div className="aspect-square rounded-xl mb-3 overflow-hidden bg-gray-50 border border-gray-50 relative">
                      {product.thumbnail ? (
                        <img
                          src={product.thumbnail}
                          alt={product.name}
                          className="w-full h-full object-cover group-hover/card:scale-105 transition-transform duration-500"
                        />
                      ) : (
                        <div className="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400 font-bold text-xs uppercase">
                          No Image
                        </div>
                      )}
                      <span className="absolute left-2.5 top-2.5 rounded-full bg-blue-600 px-2 py-0.5 text-[8px] font-black uppercase tracking-wider text-white shadow-sm shadow-blue-500/10">
                        New
                      </span>
                    </div>

                    {/* Name */}
                    <div>
                      <h3 className="font-bold text-gray-900 text-xs sm:text-sm mb-1.5 leading-snug line-clamp-2 group-hover/card:text-blue-600 transition-colors">
                        {product.name}
                      </h3>
                    </div>
                  </div>

                  {/* Actions */}
                  <div className="flex items-end justify-between gap-2 pt-2">
                    <div className="flex flex-col">
                      <span className="font-extrabold text-blue-600 text-sm leading-none">
                        {product.currencySymbol}
                        {product.price.toFixed(2)}
                      </span>
                    </div>

                    <button
                      onClick={(e) => toggleCart(product.id, e)}
                      className={`text-[10px] font-bold px-3 py-2 rounded-xl transition-all active:scale-95 flex items-center gap-1 shadow-sm border ${
                        isInCart
                          ? "bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100"
                          : "bg-white text-gray-700 border-gray-200 hover:border-blue-500 hover:text-blue-600"
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
                </div>
              );
            })
          ) : (
            <div className="w-full text-center py-8 text-gray-400 bg-gray-55 rounded-xl border border-gray-100">
              No products available
            </div>
          )}
        </div>

        {/* Right Arrow */}
        <button
          onClick={scrollRight}
          className="absolute right-[-16px] top-1/2 -translate-y-1/2 z-20 bg-white/95 backdrop-blur-sm shadow-md rounded-full p-2.5 text-gray-600 hover:text-blue-600 hover:scale-110 active:scale-95 transition-all duration-300 opacity-0 group-hover:opacity-100 hidden md:flex items-center justify-center border border-gray-150"
          aria-label="Scroll Right"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            strokeWidth="3"
            stroke="currentColor"
            className="w-4 h-4"
          >
            <path strokeLinecap="round" strokeLinejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
          </svg>
        </button>
      </div>
    </section>
  );
}
