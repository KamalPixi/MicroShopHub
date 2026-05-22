"use client";

import React, { useRef } from "react";
import Link from "next/link";

interface Category {
  id: number;
  name: string;
  subtitle?: string;
  thumbnail?: string;
}

interface CategoryHomeProps {
  title?: string;
  categories?: Category[];
}

const fallbackGradients = [
  "from-sky-500 to-indigo-600",
  "from-emerald-500 to-teal-600",
  "from-rose-500 to-fuchsia-600",
  "from-amber-400 to-orange-600",
  "from-violet-500 to-purple-700",
  "from-cyan-500 to-blue-600",
  "from-lime-500 to-green-600",
  "from-pink-500 to-red-600",
];

const defaultCategories: Category[] = [
  { id: 1, name: "Electronics", subtitle: "Latest gadgets and mobile devices" },
  { id: 2, name: "Fashion", subtitle: "Trendy shirts, dresses and shoes" },
  { id: 3, name: "Home & Kitchen", subtitle: "Cozy furniture and cookware" },
  { id: 4, name: "Fitness & Outdoors", subtitle: "Gear up for health and adventure" },
  { id: 5, name: "Books & Stationery", subtitle: "Novels, diaries and writing supplies" },
];

export default function CategoryHome({
  title = "Shop by Category",
  categories = defaultCategories,
}: CategoryHomeProps) {
  const containerRef = useRef<HTMLDivElement>(null);
  const scrollAmount = 320;

  const scrollLeft = () => {
    containerRef.current?.scrollBy({ left: -scrollAmount, behavior: "smooth" });
  };

  const scrollRight = () => {
    containerRef.current?.scrollBy({ left: scrollAmount, behavior: "smooth" });
  };

  return (
    <section className="relative mb-10 group">
      <div className="mb-4 px-1">
        <h2 className="text-xl font-extrabold text-gray-900 tracking-tight">{title}</h2>
      </div>

      <div className="relative">
        {/* Left Arrow */}
        <button
          onClick={scrollLeft}
          className="absolute left-[-16px] top-1/2 -translate-y-1/2 z-20 bg-white/95 backdrop-blur-sm shadow-md rounded-full p-2.5 hover:bg-white hover:shadow-lg hover:scale-110 active:scale-95 transition-all duration-300 hidden md:flex items-center justify-center border border-gray-150 opacity-0 group-hover:opacity-100"
          aria-label="Scroll Left"
        >
          <svg className="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M15 19l-7-7 7-7" />
          </svg>
        </button>

        {/* Categories Scroll Container */}
        <div
          ref={containerRef}
          className="overflow-x-auto scroll-smooth snap-x snap-mandatory pb-2 no-scrollbar"
        >
          <div className="flex gap-4 py-2 px-1">
            {categories.length > 0 ? (
              categories.map((category, index) => {
                const gradient = fallbackGradients[index % fallbackGradients.length];
                return (
                  <Link
                    key={category.id}
                    href={`/search/?category=${category.id}`}
                    className="flex-none w-64 md:w-72 snap-start relative overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 group/card block"
                  >
                    {category.thumbnail ? (
                      <img
                        src={category.thumbnail}
                        alt={category.name}
                        className="w-full h-40 object-cover group-hover/card:scale-105 transition-transform duration-500"
                      />
                    ) : (
                      <div
                        className={`flex h-40 items-center justify-center bg-gradient-to-br ${gradient} text-white group-hover/card:brightness-[1.03] transition-all`}
                      >
                        <span className="text-4xl font-black tracking-tight opacity-90 group-hover/card:scale-110 transition-transform duration-300">
                          {category.name.charAt(0).toUpperCase()}
                        </span>
                      </div>
                    )}

                    <div className="p-4 bg-white">
                      <h3 className="text-sm font-bold text-gray-900 group-hover/card:text-blue-600 transition-colors line-clamp-1">
                        {category.name}
                      </h3>
                      {category.subtitle && (
                        <p className="text-[11px] text-gray-500 mt-1 line-clamp-2 leading-relaxed">
                          {category.subtitle}
                        </p>
                      )}
                    </div>
                  </Link>
                );
              })
            ) : (
              <div className="w-full text-center py-8 text-gray-400 bg-gray-50 rounded-xl border border-gray-100">
                No categories available
              </div>
            )}
          </div>
        </div>

        {/* Right Arrow */}
        <button
          onClick={scrollRight}
          className="absolute right-[-16px] top-1/2 -translate-y-1/2 z-20 bg-white/95 backdrop-blur-sm shadow-md rounded-full p-2.5 hover:bg-white hover:shadow-lg hover:scale-110 active:scale-95 transition-all duration-300 hidden md:flex items-center justify-center border border-gray-150 opacity-0 group-hover:opacity-100"
          aria-label="Scroll Right"
        >
          <svg className="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>
    </section>
  );
}
