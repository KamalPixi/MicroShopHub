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
  bannerType?: "split" | "text_only" | "slider_only" | string;
  heroTitle?: string;
  heroSubtitle?: string;
  ctaLabel?: string;
  ctaUrl?: string;
  chips?: string[];
  bannerSlides?: Slide[];
  autoplayEnabled?: boolean;
  brandingColor?: string;
  secondaryColor?: string;
  accentColor?: string;
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
  bannerType = "split",
  heroTitle = "Find what fits your life",
  heroSubtitle = "Curated products, fast delivery, and a storefront built for easy browsing.",
  ctaLabel = "Shop Now",
  ctaUrl = "/search",
  chips = ["Free Shipping", "Secure Payments", "Easy Returns"],
  bannerSlides = defaultSlides,
  autoplayEnabled = true,
  brandingColor = "#2563eb",
  secondaryColor = "#64748b",
  accentColor = "#f59e0b",
}: HeroProps) {
  const [activeSlide, setActiveSlide] = useState(0);
  const [isHovered, setIsHovered] = useState(false);

  // Setup autoplay timer which pauses when hovered
  useEffect(() => {
    if (!autoplayEnabled || bannerSlides.length <= 1 || isHovered) return;

    const interval = setInterval(() => {
      setActiveSlide((prev) => (prev + 1) % bannerSlides.length);
    }, 3800); // 3800ms exact parity with blade

    return () => clearInterval(interval);
  }, [autoplayEnabled, bannerSlides.length, isHovered]);

  const showSlide = (index: number) => {
    setActiveSlide(index);
  };

  const nextSlide = () => {
    setActiveSlide((prev) => (prev + 1) % bannerSlides.length);
  };

  const prevSlide = () => {
    setActiveSlide((prev) => (prev - 1 + bannerSlides.length) % bannerSlides.length);
  };

  const handleScrollToNewsletter = (e: React.MouseEvent<HTMLAnchorElement>) => {
    e.preventDefault();
    const newsletter = document.getElementById("newsletter");
    if (newsletter) {
      newsletter.scrollIntoView({ behavior: "smooth" });
    }
  };

  const primaryBg = brandingColor || "#2563eb";
  const secondaryBg = secondaryColor || "#64748b";
  const accentBg = accentColor || "#f59e0b";

  const gradientStyle = {
    background: `linear-gradient(135deg, ${primaryBg} 0%, ${secondaryBg} 60%, ${primaryBg} 100%)`,
  };

  return (
    <section
      className="relative overflow-hidden rounded-3xl text-white mb-10 border border-white/10 shadow-[0_20px_50px_rgba(15,23,42,0.18)]"
      style={gradientStyle}
    >
      {/* Decorative ambient glowing circles */}
      <div className="absolute inset-0 opacity-20 pointer-events-none">
        <div className="absolute -top-20 -right-24 h-72 w-72 rounded-full bg-white/20 blur-3xl"></div>
        <div
          className="absolute -bottom-28 -left-16 h-80 w-80 rounded-full blur-3xl opacity-20"
          style={{ backgroundColor: accentBg }}
        ></div>
        <div className="absolute left-1/2 top-1/2 h-40 w-40 -translate-x-1/2 -translate-y-1/2 rounded-full bg-white/10 blur-2xl"></div>
      </div>

      {bannerType === "text_only" ? (
        /* ==================== 1. TEXT ONLY LAYOUT ==================== */
        <div className="relative z-10 grid gap-6 px-6 py-6 md:grid-cols-[1.15fr_0.85fr] md:px-10 md:py-8 items-center">
          <div className="flex flex-col justify-center">
            <h1 className="text-2xl md:text-4xl font-extrabold leading-tight tracking-tight">
              {heroTitle}
            </h1>
            <p className="mt-2 max-w-3xl text-sm md:text-base leading-relaxed text-white/80">
              {heroSubtitle}
            </p>

            <div className="mt-4 flex flex-wrap items-center gap-3">
              <Link
                href={ctaUrl}
                className="inline-flex items-center rounded-xl bg-white px-5 py-2.5 text-sm font-semibold shadow-sm transition hover:opacity-95"
                style={{ color: primaryBg }}
              >
                {ctaLabel}
              </Link>
              <Link
                href="/search"
                className="inline-flex items-center rounded-xl border border-white/25 bg-white/10 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-white/15"
              >
                Browse Store
              </Link>
            </div>

            {chips.length > 0 && (
              <div className="mt-4 flex flex-wrap gap-2 text-xs">
                {chips.map((chip, index) => (
                  <span
                    key={index}
                    className="rounded-full border border-white/15 bg-white/10 px-3 py-1.5 backdrop-blur-sm"
                  >
                    {chip}
                  </span>
                ))}
              </div>
            )}
          </div>

          <div className="self-center">
            <div className="rounded-2xl border border-white/15 bg-white/10 p-3.5 backdrop-blur-sm">
              <p className="text-[11px] uppercase tracking-[0.18em] text-white/70">
                Quick Actions
              </p>
              <div className="mt-2.5 space-y-2">
                <Link
                  href="/search"
                  className="group flex items-center justify-between rounded-xl bg-white/10 px-3 py-2.5 transition hover:bg-white/15"
                >
                  <div>
                    <p className="text-sm font-semibold">Browse Categories</p>
                    <p className="mt-0.5 text-[11px] text-white/75">
                      Explore our collections
                    </p>
                  </div>
                  <span
                    className="ml-3 inline-flex h-7 w-7 items-center justify-center rounded-full bg-white font-bold transition group-hover:translate-x-0.5"
                    style={{ color: primaryBg }}
                  >
                    →
                  </span>
                </Link>
                <Link
                  href="/search?sort=newest"
                  className="group flex items-center justify-between rounded-xl bg-white/10 px-3 py-2.5 transition hover:bg-white/15"
                >
                  <div>
                    <p className="text-sm font-semibold">See New Arrivals</p>
                    <p className="mt-0.5 text-[11px] text-white/75">
                      Check fresh products
                    </p>
                  </div>
                  <span
                    className="ml-3 inline-flex h-7 w-7 items-center justify-center rounded-full font-bold transition group-hover:translate-x-0.5 text-slate-900"
                    style={{ backgroundColor: accentBg }}
                  >
                    →
                  </span>
                </Link>
                <a
                  href="#newsletter"
                  onClick={handleScrollToNewsletter}
                  className="group flex items-center justify-between rounded-xl bg-white/10 px-3 py-2.5 transition hover:bg-white/15"
                >
                  <div>
                    <p className="text-sm font-semibold">Stay Updated</p>
                    <p className="mt-0.5 text-[11px] text-white/75">
                      Subscribe to newsletter
                    </p>
                  </div>
                  <span
                    className="ml-3 inline-flex h-7 w-7 items-center justify-center rounded-full bg-white font-bold transition group-hover:translate-x-0.5"
                    style={{ color: secondaryBg }}
                  >
                    →
                  </span>
                </a>
              </div>
            </div>
          </div>
        </div>
      ) : bannerType === "split" ? (
        /* ==================== 2. SPLIT LAYOUT ==================== */
        <div
          className="relative z-10 grid gap-6 px-6 py-7 md:grid-cols-[3fr_1.2fr] md:px-10 md:py-9 items-center"
          onMouseEnter={() => setIsHovered(true)}
          onMouseLeave={() => setIsHovered(false)}
        >
          {/* Left Side: Stateful image carousel slider */}
          <div className="relative overflow-hidden rounded-2xl border border-white/15 bg-white/10 aspect-[24/10] md:aspect-[24/8] backdrop-blur-sm shadow-inner group/slider">
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
                  alt={slide.alt || "Homepage banner slide"}
                  className="h-full w-full object-cover"
                />
              </Link>
            ))}

            {/* Fade background gradient overlay */}
            <div className="absolute inset-0 bg-gradient-to-t from-slate-950/35 via-transparent to-transparent pointer-events-none z-15"></div>

            {/* Dots and Navigation Arrows */}
            <div className="absolute bottom-4 left-4 right-4 flex items-center justify-between gap-3 z-20">
              <div className="flex gap-2">
                {bannerSlides.map((_, index) => (
                  <button
                    key={index}
                    type="button"
                    onClick={() => showSlide(index)}
                    className={`h-2.5 rounded-full transition-all duration-300 ${
                      index === activeSlide ? "w-8 bg-white" : "w-2.5 bg-white/45 hover:bg-white/60"
                    }`}
                    aria-label={`Go to slide ${index + 1}`}
                  ></button>
                ))}
              </div>

              {bannerSlides.length > 1 && (
                <div className="flex gap-2">
                  <button
                    type="button"
                    onClick={prevSlide}
                    className="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/15 text-white hover:bg-white/25 active:scale-95 transition-all"
                    aria-label="Previous slide"
                  >
                    <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                  </button>
                  <button
                    type="button"
                    onClick={nextSlide}
                    className="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/15 text-white hover:bg-white/25 active:scale-95 transition-all"
                    aria-label="Next slide"
                  >
                    <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                  </button>
                </div>
              )}
            </div>
          </div>

          {/* Right Side: Text content */}
          <div className="flex flex-col justify-center md:pl-2">
            <h1 className="text-2xl md:text-[1.7rem] font-extrabold leading-tight tracking-tight">
              {heroTitle}
            </h1>
            <p className="mt-2 max-w-2xl text-sm md:text-[0.85rem] leading-5 text-white/80">
              {heroSubtitle}
            </p>

            <div className="mt-4 flex flex-wrap items-center gap-3">
              <Link
                href={ctaUrl}
                className="inline-flex items-center rounded-xl bg-white px-5 py-2.5 text-sm font-semibold shadow-sm transition hover:opacity-95"
                style={{ color: primaryBg }}
              >
                {ctaLabel}
              </Link>
              <Link
                href="/search"
                className="inline-flex items-center rounded-xl border border-white/25 bg-white/10 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-white/15"
              >
                Browse Store
              </Link>
            </div>

            {chips.length > 0 && (
              <div className="mt-3.5 flex flex-wrap gap-2 text-xs">
                {chips.map((chip, index) => (
                  <span
                    key={index}
                    className="rounded-full border border-white/15 bg-white/10 px-3 py-1.5 backdrop-blur-sm"
                  >
                    {chip}
                  </span>
                ))}
              </div>
            )}
          </div>
        </div>
      ) : (
        /* ==================== 3. SLIDER ONLY / DEFAULT LAYOUT ==================== */
        <div
          className="relative z-10 px-4 py-3 md:px-8 md:py-5"
          onMouseEnter={() => setIsHovered(true)}
          onMouseLeave={() => setIsHovered(false)}
        >
          <div className="relative overflow-hidden rounded-2xl border border-white/15 bg-white/10 aspect-[21/9] md:aspect-[32/8] backdrop-blur-sm group/slider">
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
                  alt={slide.alt || "Homepage banner slide"}
                  className="h-full w-full object-cover"
                />
              </Link>
            ))}

            {/* Fade background gradient overlay */}
            <div className="absolute inset-0 bg-gradient-to-t from-slate-950/25 via-transparent to-transparent pointer-events-none z-15"></div>

            {/* Dots Center Indicators */}
            <div className="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-20">
              {bannerSlides.map((_, index) => (
                <button
                  key={index}
                  type="button"
                  onClick={() => showSlide(index)}
                  className={`h-2.5 rounded-full transition-all duration-300 ${
                    index === activeSlide ? "w-8 bg-white" : "w-2.5 bg-white/45 hover:bg-white/60"
                  }`}
                  aria-label={`Go to slide ${index + 1}`}
                ></button>
              ))}
            </div>
          </div>
        </div>
      )}
    </section>
  );
}
