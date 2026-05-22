"use client";

import React, { useEffect, useState } from "react";
import Header from "../components/Header";
import Navbar from "../components/Navbar";
import Hero from "../components/Hero";
import FlashSale from "../components/FlashSale";
import CategoryHome from "../components/CategoryHome";
import FeaturedProducts from "../components/FeaturedProducts";
import NewArrivals from "../components/NewArrivals";
import Footer from "../components/Footer";
import { api, HomepageData, ProductData } from "../utils/api";
import { addToCart, isInCart } from "../utils/cart";

// Dynamic SPA Subpages
import ProductClientPage from "../components/ProductClientPage";
import CartPage from "./cart/page";
import CheckoutPage from "./checkout/page";
import SearchPage from "./search/page";
import FlashSalePage from "./flash-sale/page";

export function HomePageContent() {
  const [data, setData] = useState<HomepageData | null>(null);
  const [flashSaleProducts, setFlashSaleProducts] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [newsletterEmail, setNewsletterEmail] = useState("");
  const [newsletterStatus, setNewsletterStatus] = useState<{
    type: "success" | "error" | "";
    message: string;
  }>({ type: "", message: "" });
  const [newsletterLoading, setNewsletterLoading] = useState(false);

  // Fetch Homepage Data
  useEffect(() => {
    async function loadData() {
      try {
        const homepageData = await api.fetchHomepage();
        setData(homepageData);

        // If there's an active flash sale, fetch the participating products
        if (homepageData.flash_sale) {
          try {
            const saleRes = await api.fetchFlashSale();
            if (saleRes && saleRes.products) {
              setFlashSaleProducts(saleRes.products);
            }
          } catch (saleErr) {
            console.error("Failed to load flash sale products", saleErr);
          }
        }
      } catch (err) {
        console.error("Failed to load storefront homepage data", err);
      } finally {
        setLoading(false);
      }
    }
    loadData();
  }, []);

  const handleNewsletterSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!newsletterEmail) return;

    setNewsletterLoading(true);
    setNewsletterStatus({ type: "", message: "" });

    try {
      const res = await api.subscribeNewsletter(newsletterEmail);
      setNewsletterStatus({
        type: "success",
        message: res.message || "Successfully subscribed to our newsletter!",
      });
      setNewsletterEmail("");
    } catch (err: any) {
      setNewsletterStatus({
        type: "error",
        message: err.message || "Newsletter subscription failed. Please try again.",
      });
    } finally {
      setNewsletterLoading(false);
    }
  };

  // Skeleton loading structure for premium look
  if (loading) {
    return (
      <div className="min-h-screen flex flex-col bg-[#f4f7fb]">
        <Header storeName="ShopHub" storeSlogan="Loading storefront..." cartCount={0} />
        <Navbar categories={[]} />
        <main className="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-8 pb-12">
          {/* Skeleton Hero Banner */}
          <div className="w-full h-64 md:h-80 rounded-3xl bg-slate-200 animate-pulse mb-8"></div>
          {/* Skeleton Section */}
          <div className="space-y-6">
            <div className="h-6 w-48 bg-slate-200 rounded animate-pulse"></div>
            <div className="flex gap-4 overflow-x-auto no-scrollbar">
              {[1, 2, 3, 4].map((i) => (
                <div key={i} className="w-[200px] h-64 bg-slate-100 border border-slate-200/50 rounded-2xl flex-shrink-0 animate-pulse"></div>
              ))}
            </div>
          </div>
        </main>
        <Footer storeName="ShopHub" />
      </div>
    );
  }

  const settings = data?.settings || {};
  const currencySymbol = data?.currency?.symbol || "$";

  // Helper mapper to transform backend product arrays to storefront component structures
  const mapProductProps = (prod: ProductData) => {
    const hasSale = prod.sale_price !== null && prod.sale_price !== undefined;
    return {
      id: prod.id,
      name: prod.name,
      slug: prod.slug,
      price: hasSale ? (prod.sale_price as number) : prod.price,
      originalPrice: hasSale ? prod.price : undefined,
      currencySymbol: prod.currency_symbol || currencySymbol,
      thumbnail: prod.thumbnail_url,
      isSale: hasSale,
    };
  };

  // Helper mapper for flash sale items
  const mapFlashSaleProductProps = (prod: ProductData) => {
    const hasSale = prod.sale_price !== null && prod.sale_price !== undefined;
    const price = hasSale ? (prod.sale_price as number) : prod.price;
    const originalPrice = prod.price;
    const discountPercent = prod.discount_percentage || Math.round(((originalPrice - price) / originalPrice) * 100);

    return {
      id: prod.id,
      name: prod.name,
      slug: prod.slug,
      price,
      originalPrice,
      discountPercent: discountPercent > 0 ? discountPercent : 10,
      currencySymbol: prod.currency_symbol || currencySymbol,
      thumbnail: prod.thumbnail_url,
    };
  };

  const banners = data?.banners?.map((b, index) => ({
    id: index + 1,
    imageUrl: b.image_url,
    linkUrl: b.link_url || "/search",
    alt: b.alt || "Storefront Promo slide",
  })) || [];

  const categories = data?.categories?.map((cat) => ({
    id: cat.id,
    name: cat.name,
    thumbnail: cat.thumbnail_url,
  })) || [];

  const featured = data?.featured_products?.map(mapProductProps) || [];
  const arrivals = data?.new_arrivals?.map(mapProductProps) || [];

  return (
    <div className="min-h-screen flex flex-col bg-[#f4f7fb]">
      {/* Dynamic Header */}
      <Header
        storeName={settings.store_name || "ShopHub"}
        storeSlogan={settings.store_slogan || "Curated products • Express shipping"}
      />

      {/* Categories Navigation Bar */}
      <Navbar />

      {/* Main Core Store Layout */}
      <main className="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-6 pb-12">
        {/* Banner Hero Section */}
        {settings.home_hero_enabled !== false && (
          <Hero
            bannerType={settings.home_banner_type}
            heroTitle={settings.home_hero_title}
            heroSubtitle={settings.home_hero_subtitle}
            ctaLabel={settings.home_hero_cta_label}
            ctaUrl={settings.home_hero_cta_url}
            chips={settings.home_banner_chips}
            bannerSlides={banners}
            autoplayEnabled={settings.home_banner_autoplay_enabled !== false}
            brandingColor={settings.branding_color}
            secondaryColor={settings.secondary_color}
            accentColor={settings.accent_color}
          />
        )}

        {/* Dynamic Flash Sale */}
        {data?.flash_sale && flashSaleProducts.length > 0 && (
          <FlashSale
            title={data.flash_sale.title}
            subtitle={data.flash_sale.subtitle || data.flash_sale.description}
            endsAtISO={data.flash_sale.ends_at}
            products={flashSaleProducts.map(mapFlashSaleProductProps)}
          />
        )}

        {/* Category List Scrollbar */}
        {categories.length > 0 && (
          <CategoryHome categories={categories} />
        )}

        {/* Featured Products */}
        {featured.length > 0 && (
          <FeaturedProducts products={featured} />
        )}

        {/* New Arrivals */}
        {arrivals.length > 0 && (
          <NewArrivals products={arrivals} />
        )}

        {/* Dynamic Call-to-action Newsletter Banner */}
        <section className="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-3xl p-8 text-center text-white mb-10 shadow-lg relative overflow-hidden">
          <div className="absolute inset-0 opacity-10 pointer-events-none">
            <div className="absolute -top-12 -left-12 h-40 w-40 rounded-full bg-white blur-xl"></div>
            <div className="absolute -bottom-16 -right-16 h-48 w-48 rounded-full bg-white blur-2xl"></div>
          </div>
          <div className="relative z-10 max-w-xl mx-auto">
            <h2 className="text-xl font-extrabold mb-2 uppercase tracking-wide">Stay Updated</h2>
            <p className="text-white/80 text-xs mb-5">
              Subscribe for new arrivals, exclusive offers, and restock alerts directly to your inbox.
            </p>

            {newsletterStatus.type && (
              <div
                className={`mb-4 px-4 py-2 text-xs rounded-xl font-bold transition-all ${
                  newsletterStatus.type === "success"
                    ? "bg-emerald-500/25 text-emerald-100 border border-emerald-500/30"
                    : "bg-rose-500/25 text-rose-100 border border-rose-500/30"
                }`}
              >
                {newsletterStatus.message}
              </div>
            )}

            <form onSubmit={handleNewsletterSubmit} className="flex flex-col sm:flex-row gap-2 max-w-md mx-auto relative">
              {newsletterLoading && (
                <div className="absolute inset-0 bg-slate-900/40 rounded-xl flex items-center justify-center z-10">
                  <div className="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></div>
                </div>
              )}
              <input
                type="email"
                placeholder="Enter your email address"
                required
                value={newsletterEmail}
                onChange={(e) => setNewsletterEmail(e.target.value)}
                className="flex-grow h-10 px-4 rounded-xl text-xs text-gray-800 bg-white/95 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
              />
              <button
                type="submit"
                className="h-10 px-6 rounded-xl bg-slate-900 hover:bg-slate-950 font-bold text-xs uppercase tracking-wider transition-all active:scale-95"
              >
                Subscribe
              </button>
            </form>
          </div>
        </section>
      </main>

      {/* Premium Footer */}
      <Footer
        storeName={settings.store_name || "ShopHub"}
        storeSlogan={settings.store_slogan || "Curated products • Express shipping"}
        aboutDescription={settings.store_about}
        supportEmail={settings.support_email}
        supportPhone={settings.support_phone}
        supportHours1={settings.support_hours_weekdays}
        supportHours2={settings.support_hours_weekends}
      />
    </div>
  );
}

function adjustColorBrightness(hex: string, percent: number) {
  let num = parseInt(hex.replace("#", ""), 16),
    amt = Math.round(2.55 * percent),
    R = (num >> 16) + amt,
    G = ((num >> 8) & 0x00ff) + amt,
    B = (num & 0x0000ff) + amt;
  return (
    "#" +
    (
      0x1000000 +
      (R < 255 ? (R < 0 ? 0 : R) : 255) * 0x10000 +
      (G < 255 ? (G < 0 ? 0 : G) : 255) * 0x100 +
      (B < 255 ? (B < 0 ? 0 : B) : 255)
    )
      .toString(16)
      .slice(1)
  );
}

function hexToRgba(hex: string, alpha: number) {
  let c = hex.replace("#", "");
  if (c.length === 3) {
    c = c.split("").map((x) => x + x).join("");
  }
  const r = parseInt(c.substring(0, 2), 16);
  const g = parseInt(c.substring(2, 4), 16);
  const b = parseInt(c.substring(4, 6), 16);
  return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

export default function Home() {
  const [pathname, setPathname] = useState("");

  // Dynamically load dynamic branding colors from settings on mount
  useEffect(() => {
    async function loadBrandingColors() {
      try {
        const homepageData = await api.fetchHomepage();
        if (homepageData && homepageData.settings) {
          const settings = homepageData.settings;
          const primary = settings.branding_color || "#ee4d2d";
          const secondary = settings.secondary_color || "#ff5722";
          const accent = settings.accent_color || "#f59e0b";

          const primaryHover = settings.branding_color
            ? adjustColorBrightness(settings.branding_color, -12)
            : "#d03e22";

          const primaryLight = hexToRgba(primary, 0.05);
          const primaryLightBorder = hexToRgba(primary, 0.15);

          document.documentElement.style.setProperty("--primary", primary);
          document.documentElement.style.setProperty("--primary-hover", primaryHover);
          document.documentElement.style.setProperty("--primary-light", primaryLight);
          document.documentElement.style.setProperty("--primary-light-border", primaryLightBorder);
          document.documentElement.style.setProperty("--secondary", secondary);
          document.documentElement.style.setProperty("--accent", accent);
        }
      } catch (err) {
        console.error("Failed to load dynamic branding colors", err);
      }
    }
    loadBrandingColors();
  }, []);

  useEffect(() => {
    setPathname(window.location.pathname);

    const handleLinkClick = (e: MouseEvent) => {
      let target = e.target as HTMLElement | null;
      while (target && target.tagName !== "A") {
        target = target.parentElement;
      }
      if (target && target.tagName === "A") {
        const href = target.getAttribute("href");
        if (href && href.startsWith("/") && !href.startsWith("/api") && !href.startsWith("/admin")) {
          e.preventDefault();
          window.history.pushState(null, "", href);
          window.dispatchEvent(new PopStateEvent("popstate"));
        }
      }
    };

    const handlePopState = () => {
      setPathname(window.location.pathname);
    };

    document.addEventListener("click", handleLinkClick);
    window.addEventListener("popstate", handlePopState);

    return () => {
      document.removeEventListener("click", handleLinkClick);
      window.removeEventListener("popstate", handlePopState);
    };
  }, []);

  if (!pathname) {
    return (
      <div className="min-h-screen bg-[#f4f7fb] flex items-center justify-center">
        <div className="animate-spin rounded-full h-8 w-8 border-4 border-blue-600 border-t-transparent"></div>
      </div>
    );
  }

  if (pathname === "/" || pathname === "") {
    return <HomePageContent />;
  }

  if (pathname.includes("/product/")) {
    return <ProductClientPage />;
  }

  const cleanPath = pathname.replace(/\/$/, "");

  if (cleanPath === "/cart") {
    return <CartPage />;
  }

  if (cleanPath === "/checkout") {
    return <CheckoutPage />;
  }

  if (cleanPath === "/search") {
    return <SearchPage />;
  }

  if (cleanPath === "/flash-sale") {
    return <FlashSalePage />;
  }

  return (
    <div className="min-h-screen flex flex-col bg-[#f4f7fb]">
      <Header storeName="ShopHub" />
      <Navbar />
      <main className="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-16 pb-12 text-center">
        <div className="h-16 w-16 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-4 font-black text-2xl">
          🔍
        </div>
        <h3 className="text-lg font-bold text-gray-900 mb-1.5 uppercase tracking-wide">Page Not Found</h3>
        <p className="text-xs text-gray-500 mb-5 max-w-md mx-auto leading-relaxed">
          The requested page is unavailable or has been moved. Use the menu or link below to get back on track.
        </p>
        <button
          onClick={() => {
            window.history.pushState(null, "", "/");
            window.dispatchEvent(new PopStateEvent("popstate"));
          }}
          className="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs uppercase tracking-wider shadow-md shadow-blue-500/10 transition-all active:scale-95"
        >
          Go Back Home
        </button>
      </main>
      <Footer storeName="ShopHub" />
    </div>
  );
}
