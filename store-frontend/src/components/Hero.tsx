"use client";

import React, { useState, useEffect } from "react";
import Link from "next/link";

interface Slide {
  id: number;
  imageUrl: string;
  linkUrl: string;
  alt: string;
}

interface HeroProps {
  heroTitle?: string;
  heroSubtitle?: string;
  ctaLabel?: string;
  ctaUrl?: string;
  chips?: string[];
  bannerSlides?: Slide[];
  autoplayEnabled?: boolean;
}

const defaultSlides: Slide[] = [
  {
    id: 1,
    imageUrl: "https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=1200&q=80",
    linkUrl: "/search",
    alt: "Modern workspace tech deals",
  },
  {
    id: 2,
    imageUrl: "https://images.unsplash.com/photo-1483985988355-763728e1935b?w=1200&q=80",
    linkUrl: "/search?sort=newest",
    alt: "Latest seasonal fashion items",
  },
  {
    id: 3,
    imageUrl: "https://images.unsplash.com/photo-1513694203232-719a280e022f?w=1200&q=80",
    linkUrl: "/search?category=3",
    alt: "Premium home living decor",
  },
];

export default function Hero({
  heroTitle = "Find what fits your life",
  heroSubtitle = "Curated products, fast delivery, and a storefront built for easy browsing.",
  ctaLabel = "Shop Now",
  ctaUrl = "/search",
  chips = ["Free Shipping", "Secure Payments", "Easy Returns"],
  bannerSlides = defaultSlides,
  autoplayEnabled = true,
}: HeroProps) {
  const [activeSlide, setActiveSlide] = useState(0);

  useEffect(() => {
    if (!autoplayEnabled || bannerSlides.length <= 1) return;

    const interval = setInterval(() => {
      setActiveSlide((prev) => (prev + 1) % bannerSlides.length);
    }, 4500);

    return () => clearInterval(interval);
  }, [autoplayEnabled, bannerSlides.length]);

  const showSlide = (index: number) => {
    setActiveSlide(index);
  };

  const nextSlide = () => {
    setActiveSlide((prev) => (prev + 1) % bannerSlides.length);
  };

  const prevSlide = () => {
    setActiveSlide((prev) => (prev - 1 + bannerSlides.length) % bannerSlides.length);
  };

  return (
    <section className="relative overflow-hidden rounded-3xl text-white mb-10 border border-white/10 shadow-lg bg-gradient-to-br from-blue-600 via-indigo-600 to-blue-700">
      <div className="absolute inset-0 opacity-20 pointer-events-none">
        <div className="absolute -top-20 -right-24 h-72 w-72 rounded-full bg-white/20 blur-3xl"></div>
        <div className="absolute -bottom-28 -left-16 h-80 w-80 rounded-full bg-amber-400 blur-3xl opacity-20"></div>
        <div className="absolute left-1/2 top-1/2 h-40 w-40 -translate-x-1/2 -translate-y-1/2 rounded-full bg-white/10 blur-2xl"></div>
      </div>

      <div className="relative z-10 grid gap-6 p-6 md:grid-cols-[1.6fr_1fr] md:p-9 items-center">
        {/* Banner Image Carousel */}
        <div className="relative overflow-hidden rounded-2xl border border-white/15 bg-white/10 aspect-[21/9] backdrop-blur-sm shadow-inner group/slider">
          {bannerSlides.map((slide, index) => (
            <Link
              key={slide.id}
              href={slide.linkUrl}
              className={`absolute inset-0 block transition-opacity duration-700 ease-out ${
                index === activeSlide ? "opacity-100 z-10" : "opacity-0 z-0 pointer-events-none"
              }`}
            >
              <img
                src={slide.imageUrl}
                alt={slide.alt}
                className="h-full w-full object-cover brightness-[0.95]"
              />
            </Link>
          ))}

          {/* Dots Indicator */}
          <div className="absolute bottom-4 left-4 right-4 flex items-center justify-between gap-3 z-20">
            <div className="flex gap-1.5">
              {bannerSlides.map((_, index) => (
                <button
                  key={index}
                  type="button"
                  onClick={() => showSlide(index)}
                  className={`h-2 rounded-full transition-all duration-300 ${
                    index === activeSlide ? "w-6 bg-white" : "w-2 bg-white/40 hover:bg-white/60"
                  }`}
                  aria-label={`Go to slide ${index + 1}`}
                ></button>
              ))}
            </div>

            {bannerSlides.length > 1 && (
              <div className="flex gap-1.5 opacity-0 group-hover/slider:opacity-100 transition-opacity duration-200">
                <button
                  type="button"
                  onClick={prevSlide}
                  className="inline-flex h-7 w-7 items-center justify-center rounded-full bg-black/20 text-white hover:bg-black/35 hover:scale-105 active:scale-95 transition-all"
                  aria-label="Previous slide"
                >
                  <svg className="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M15 19l-7-7 7-7" />
                  </svg>
                </button>
                <button
                  type="button"
                  onClick={nextSlide}
                  className="inline-flex h-7 w-7 items-center justify-center rounded-full bg-black/20 text-white hover:bg-black/35 hover:scale-105 active:scale-95 transition-all"
                  aria-label="Next slide"
                >
                  <svg className="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M9 5l7 7-7 7" />
                  </svg>
                </button>
              </div>
            )}
          </div>
        </div>

        {/* Hero Text Content */}
        <div className="flex flex-col justify-center">
          <h1 className="text-xl md:text-3xl font-black leading-tight tracking-tight text-white uppercase">
            {heroTitle}
          </h1>
          <p className="mt-2 max-w-xl text-xs md:text-sm leading-relaxed text-white/80">
            {heroSubtitle}
          </p>

          <div className="mt-5 flex flex-wrap items-center gap-3">
            <Link
              href={ctaUrl}
              className="inline-flex items-center rounded-xl bg-white px-5 py-2.5 text-xs font-extrabold text-blue-600 shadow-sm transition-all hover:scale-[1.02] active:scale-95 hover:opacity-95"
            >
              {ctaLabel}
            </Link>
            <Link
              href="/search"
              className="inline-flex items-center rounded-xl border border-white/20 bg-white/5 px-5 py-2.5 text-xs font-extrabold text-white transition-all hover:bg-white/10 active:scale-95"
            >
              Browse Store
            </Link>
          </div>

          {chips.length > 0 && (
            <div className="mt-5 flex flex-wrap gap-1.5 text-[9px] font-bold">
              {chips.map((chip, index) => (
                <span
                  key={index}
                  className="rounded-full border border-white/10 bg-white/5 px-3 py-1.5 backdrop-blur-sm tracking-wide uppercase"
                >
                  {chip}
                </span>
              ))}
            </div>
          )}
        </div>
      </div>
    </section>
  );
}
