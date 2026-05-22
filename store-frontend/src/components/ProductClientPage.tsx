"use client";

import React, { useEffect, useState } from "react";
import Header from "./Header";
import Navbar from "./Navbar";
import Footer from "./Footer";
import { api, ProductDetailResponse, ProductData } from "../utils/api";
import { addToCart, isInCart } from "../utils/cart";
import { useNotification } from "@/context/NotificationContext";

export default function ProductClientPage() {
  const { showNotification } = useNotification();
  const [pathname, setPathname] = useState("");

  useEffect(() => {
    setPathname(window.location.pathname);
    const handlePopState = () => {
      setPathname(window.location.pathname);
    };
    window.addEventListener("popstate", handlePopState);
    return () => window.removeEventListener("popstate", handlePopState);
  }, []);

  const slug = pathname ? pathname.split("/product/")[1]?.replace(/\/$/, "") : "";

  const [data, setData] = useState<ProductDetailResponse | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  
  // Gallery Image State
  const [activeImage, setActiveImage] = useState("");
  const [isLightboxOpen, setIsLightboxOpen] = useState(false);
  const [lightboxIndex, setLightboxIndex] = useState(0);
  
  // Variations State
  const [selectedAttributes, setSelectedAttributes] = useState<Record<string, string>>({});
  const [matchedVariation, setMatchedVariation] = useState<any>(null);
  const [quantity, setQuantity] = useState(1);
  const [addedMessage, setAddedMessage] = useState(false);
  const [buyNowLoading, setBuyNowLoading] = useState(false);

  const images = data ? [data.product.thumbnail_url, ...(data.product.image_urls || [])].filter(Boolean) : [];

  useEffect(() => {
    if (!slug) return;
    setLoading(true);
    setError("");

    api.fetchProductDetail(slug)
      .then((res) => {
        setData(res);
        if (res.product?.thumbnail_url) {
          setActiveImage(res.product.thumbnail_url);
        } else if (res.product?.image_urls?.length > 0) {
          setActiveImage(res.product.image_urls[0]);
        }
        
        // Initialize first available options
        const defaultAttrs: Record<string, string> = {};
        res.options.forEach((opt) => {
          if (opt.values.length > 0) {
            defaultAttrs[opt.name] = opt.values[0].value;
          }
        });
        setSelectedAttributes(defaultAttrs);
      })
      .catch((err) => {
        console.error("Failed to load product detail", err);
        setError("We couldn't retrieve this product's details. It may not exist.");
      })
      .finally(() => {
        setLoading(false);
      });
  }, [slug]);

  // Match active variation based on attribute selection
  useEffect(() => {
    if (!data || !data.variations || data.variations.length === 0) {
      setMatchedVariation(null);
      return;
    }

    // Find the variation that matches all selected attributes
    const match = data.variations.find((v) => {
      // The variation contains `value_ids` list. We map selected attributes to option value IDs.
      const matchAll = data.options.every((opt) => {
        const selectedValName = selectedAttributes[opt.name];
        const valObj = opt.values.find((val) => val.value === selectedValName);
        if (!valObj) return false;
        return v.value_ids.includes(valObj.id);
      });
      return matchAll;
    });

    setMatchedVariation(match || null);
  }, [selectedAttributes, data]);

  // Lightbox handlers and navigation state synchronization
  const handlePrevImage = (e?: React.MouseEvent | React.KeyboardEvent) => {
    e?.stopPropagation();
    if (images.length === 0) return;
    const prevIdx = (lightboxIndex - 1 + images.length) % images.length;
    setLightboxIndex(prevIdx);
    if (images[prevIdx]) {
      setActiveImage(images[prevIdx]);
    }
  };

  const handleNextImage = (e?: React.MouseEvent | React.KeyboardEvent) => {
    e?.stopPropagation();
    if (images.length === 0) return;
    const nextIdx = (lightboxIndex + 1) % images.length;
    setLightboxIndex(nextIdx);
    if (images[nextIdx]) {
      setActiveImage(images[nextIdx]);
    }
  };

  // Sync lightboxIndex when activeImage changes
  useEffect(() => {
    const idx = images.indexOf(activeImage);
    if (idx !== -1) {
      setLightboxIndex(idx);
    }
  }, [activeImage, images]);

  // Handle keyboard events and scrolling locks
  useEffect(() => {
    if (!isLightboxOpen) return;
    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.key === "Escape") setIsLightboxOpen(false);
      if (e.key === "ArrowLeft") handlePrevImage();
      if (e.key === "ArrowRight") handleNextImage();
    };
    window.addEventListener("keydown", handleKeyDown);
    
    // Lock underlying page scroll
    const originalOverflow = document.body.style.overflow;
    document.body.style.overflow = "hidden";
    
    return () => {
      window.removeEventListener("keydown", handleKeyDown);
      document.body.style.overflow = originalOverflow || "unset";
    };
  }, [isLightboxOpen, lightboxIndex, images]);

  const handleAttributeChange = (optName: string, valName: string) => {
    setSelectedAttributes((prev) => ({
      ...prev,
      [optName]: valName,
    }));
  };

  const handleAddToCart = () => {
    if (!data) return;
    
    const productId = data.product.id;
    const variationId = matchedVariation ? matchedVariation.id : null;
    
    addToCart(productId, variationId, quantity, selectedAttributes);
    
    showNotification(`🛒 Added ${data.product.name} to your cart!`, "success");
    
    setAddedMessage(true);
    setTimeout(() => {
      setAddedMessage(false);
    }, 2500);
  };

  const handleBuyNow = () => {
    if (!data) return;
    
    setBuyNowLoading(true);
    const productId = data.product.id;
    const variationId = matchedVariation ? matchedVariation.id : null;
    
    addToCart(productId, variationId, quantity, selectedAttributes);
    
    showNotification(`🛒 Added ${data.product.name} to your cart!`, "success");
    
    setTimeout(() => {
      window.history.pushState(null, "", "/checkout");
      window.dispatchEvent(new PopStateEvent("popstate"));
      setBuyNowLoading(false);
    }, 500);
  };

  if (loading) {
    return (
      <div className="min-h-screen flex flex-col bg-[#f4f7fb]">
        <Header />
        <Navbar />
        <main className="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-8 pb-12">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div className="aspect-square bg-slate-100 rounded-3xl animate-pulse"></div>
            <div className="space-y-4">
              <div className="h-4 w-24 bg-slate-200 rounded animate-pulse"></div>
              <div className="h-8 w-3/4 bg-slate-200 rounded animate-pulse"></div>
              <div className="h-6 w-1/3 bg-slate-200 rounded animate-pulse"></div>
              <div className="h-20 w-full bg-slate-200 rounded animate-pulse"></div>
            </div>
          </div>
        </main>
        <Footer />
      </div>
    );
  }

  if (error || !data) {
    return (
      <div className="min-h-screen flex flex-col bg-[#f4f7fb]">
        <Header />
        <Navbar />
        <main className="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-16 pb-12 text-center">
          <div className="h-16 w-16 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-4 font-black text-2xl">
            ⚠️
          </div>
          <h3 className="text-lg font-bold text-gray-900 mb-1.5 uppercase tracking-wide">Product Not Found</h3>
          <p className="text-xs text-gray-500 mb-5 max-w-md mx-auto leading-relaxed">
            {error || "The requested item is unavailable or doesn't exist in our active database."}
          </p>
          <a
            href="/"
            className="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs uppercase tracking-wider shadow-md shadow-blue-500/10 transition-all active:scale-95 inline-block"
          >
            Go Back Home
          </a>
        </main>
        <Footer />
      </div>
    );
  }

  const { product, options, related_products, reviews, flash_sale } = data;
  
  // Resolve correct current pricing
  let activePrice = product.sale_price !== null ? product.sale_price : product.price;
  let originalPrice = product.sale_price !== null ? product.price : null;
  let activeSku = product.sku;
  let activeStock = product.stock;
  
  if (product.has_variations && matchedVariation) {
    activePrice = matchedVariation.sale_price !== null ? matchedVariation.sale_price : matchedVariation.price;
    originalPrice = matchedVariation.sale_price !== null ? matchedVariation.price : null;
    activeSku = matchedVariation.sku || product.sku;
    activeStock = matchedVariation.stock;
  }

  const hasDiscount = originalPrice !== null;
  const isOutOfStock = activeStock <= 0;
  const discountAmount = hasDiscount ? (originalPrice as number) - activePrice : 0;
  const discountPercentage = hasDiscount ? Math.round((discountAmount / (originalPrice as number)) * 100) : 0;

  // images array already defined at top level

  return (
    <div className="min-h-screen flex flex-col bg-[#f4f7fb]">
      <Header />
      <Navbar />

      <main className="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-6 pb-12">
        {/* Core Product Information Card */}
        <div className="bg-white rounded-3xl border border-gray-100 p-6 md:p-8 shadow-sm grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 mb-10">
          
          {/* LEFT: Galleries Display */}
          <div className="space-y-4">
            <div 
              className="aspect-[4/3] rounded-2xl bg-gray-50 border border-gray-50 overflow-hidden relative shadow-inner group/img cursor-zoom-in animate-in fade-in duration-300"
              onClick={() => setIsLightboxOpen(true)}
              title="Click to view full screen"
            >
              {activeImage ? (
                <img src={activeImage} alt={product.name} className="w-full h-full object-cover transition-transform duration-500 group-hover/img:scale-105" />
              ) : (
                <div className="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400 font-bold text-xs uppercase">No Product Image</div>
              )}

              {/* Magnifying expand overlay icon */}
              {activeImage && (
                <div className="absolute inset-0 bg-black/0 hover:bg-black/10 transition-colors flex items-center justify-center opacity-0 group-hover/img:opacity-100 duration-300 z-10">
                  <div className="bg-white/90 backdrop-blur-md text-gray-900 rounded-full p-3 shadow-lg transform translate-y-2 group-hover/img:translate-y-0 transition-all duration-300">
                    <svg className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="2.5" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.637 10.637Z" />
                    </svg>
                  </div>
                </div>
              )}

              {flash_sale && (
                <span className="absolute left-3 top-3 inline-flex items-center gap-1.5 rounded-full bg-rose-600 px-3 py-1 text-[9px] font-black uppercase tracking-wider text-white shadow-sm z-20">
                  ⚡ Flash Deal
                </span>
              )}
            </div>
            {/* Gallery strips */}
            {images.length > 1 && (
              <div className="flex gap-2 overflow-x-auto pb-1 no-scrollbar">
                {images.map((img, idx) => (
                  <button
                    key={idx}
                    onClick={() => setActiveImage(img)}
                    className={`flex-none h-14 w-18 rounded-lg overflow-hidden border-2 bg-gray-50 transition-all ${
                      activeImage === img ? "border-blue-600 scale-95" : "border-gray-100 hover:border-gray-300"
                    }`}
                  >
                    <img src={img} alt="Gallery item" className="h-full w-full object-cover" />
                  </button>
                ))}
              </div>
            )}
          </div>

          {/* RIGHT: Parameters Block */}
          <div className="flex flex-col justify-between">
            <div className="space-y-5">
              
              {/* Product Info Badges */}
              <div className="flex items-center gap-2">
                <span className="rounded-full bg-slate-900 px-2.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-white">
                  SKU: {activeSku || "N/A"}
                </span>
                
                {isOutOfStock ? (
                  <span className="rounded-full bg-rose-50 border border-rose-100 px-2.5 py-0.5 text-[9px] font-black uppercase tracking-wider text-rose-600">
                    Out of Stock
                  </span>
                ) : (
                  <span className="rounded-full bg-emerald-50 border border-emerald-100 px-2.5 py-0.5 text-[9px] font-black uppercase tracking-wider text-emerald-600">
                    In Stock ({activeStock} items)
                  </span>
                )}
              </div>

              {/* Title & Slogan */}
              <div>
                <h1 className="text-xl md:text-2xl font-black text-gray-900 leading-tight uppercase tracking-tight">
                  {product.name}
                </h1>
                {product.average_rating > 0 && (
                  <div className="flex items-center gap-1.5 mt-2">
                    <span className="text-xs font-black text-amber-500">★</span>
                    <span className="text-xs font-black text-gray-800">{product.average_rating.toFixed(1)}</span>
                    <span className="text-[10px] text-gray-400 font-bold uppercase">({reviews.length} reviews)</span>
                  </div>
                )}
              </div>

              {/* Pricing Blocks */}
              <div className="rounded-2xl border border-gray-200 bg-gradient-to-br from-gray-50 to-white p-3 sm:p-4 space-y-2 mb-4">
                <p className="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Price</p>
                <div className="flex flex-wrap items-end gap-3">
                  <span className="text-3xl font-bold text-blue-600 transition-all duration-300" role="status" aria-label="Product price">
                    {product.currency_symbol || "$"}{activePrice.toFixed(2)}
                  </span>
                  {hasDiscount && (
                    <>
                      <span className="text-base text-gray-400 line-through">
                        {product.currency_symbol || "$"}{originalPrice?.toFixed(2)}
                      </span>
                      <span className="text-xs font-semibold text-green-700 bg-green-100 px-2 py-1 rounded-full">
                        Save {discountPercentage}%
                      </span>
                    </>
                  )}
                </div>
                {flash_sale && (
                  <div className="inline-flex items-center gap-1.5 text-xs font-medium text-rose-700 bg-rose-50 border border-rose-200 px-2.5 py-1 rounded-md">
                    <svg className="w-3.5 h-3.5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    ⚡ Flash Deal
                  </div>
                )}
                {product.has_variations && Object.keys(selectedAttributes).length === 0 && (
                  <div className="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 bg-blue-50 border border-blue-200 px-2.5 py-1 rounded-md">
                    <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Choose options to see price
                  </div>
                )}
                {matchedVariation && product.has_variations && (
                  <div className="inline-flex items-center gap-1.5 text-xs font-medium text-green-700 bg-green-50 border border-green-200 px-2.5 py-1 rounded-md">
                    <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Final price for selected options
                  </div>
                )}
              </div>

              {/* Dynamic Variations Selector */}
              {product.has_variations && options.length > 0 && (
                <div className="space-y-4 p-4 rounded-2xl border bg-gray-50/70 border-gray-200 shadow-sm transition-all duration-300 mb-4">
                  {options.map((opt) => {
                    const currentSelected = selectedAttributes[opt.name];
                    return (
                      <div key={opt.id} className="space-y-2">
                        <label className="block text-[10px] font-black uppercase tracking-wider text-gray-500">
                          Select {opt.name}
                        </label>
                        <div className="flex flex-wrap gap-1.5">
                          {opt.values.map((v) => (
                            <button
                              key={v.id}
                              type="button"
                              onClick={() => handleAttributeChange(opt.name, v.value)}
                              className={`px-3 py-1.5 rounded-lg text-xs font-bold border transition-all active:scale-95 ${
                                currentSelected === v.value
                                  ? "bg-slate-900 border-slate-900 text-white shadow-sm"
                                  : "bg-white border-gray-200 text-gray-600 hover:border-gray-300"
                              }`}
                            >
                              {v.value}
                            </button>
                          ))}
                        </div>
                      </div>
                    );
                  })}
                </div>
              )}
            </div>

            {/* Actions Block */}
            <div className="space-y-4 pt-4 border-t border-gray-100 mt-6">
              
              {addedMessage && (
                <div className="rounded-2xl bg-emerald-50 border border-emerald-100 p-3 text-xs text-emerald-600 font-bold animate-in slide-in-from-bottom-2">
                  ✅ Item successfully added to your dynamic Cart!
                </div>
              )}

              {/* Quantity Selector & Total Price - Same Box */}
              <div className="bg-white border border-gray-200 rounded-xl p-3">
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-6">
                  
                  {/* Quantity Selector */}
                  <div className="flex-1">
                    <label className="block text-[11px] font-bold text-gray-900 mb-2">
                      Quantity
                    </label>
                    <div className="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-gray-50 p-1">
                      <button
                        onClick={() => setQuantity((q) => Math.max(1, q - 1))}
                        disabled={isOutOfStock || quantity <= 1}
                        type="button"
                        className="h-10 w-10 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-650 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                        aria-label="Decrease quantity"
                      >
                        <span className="text-lg">-</span>
                      </button>
                      <input
                        type="text"
                        value={quantity}
                        className="w-16 sm:w-20 h-10 text-center font-semibold border border-gray-300 rounded-lg bg-white text-gray-900 focus:outline-none"
                        aria-label="Quantity"
                        readOnly
                      />
                      <button
                        onClick={() => setQuantity((q) => Math.min(activeStock, q + 1))}
                        disabled={isOutOfStock || quantity >= activeStock}
                        type="button"
                        className="h-10 w-10 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-650 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                        aria-label="Increase quantity"
                      >
                        <span className="text-lg">+</span>
                      </button>
                    </div>
                  </div>

                  {/* Divider */}
                  <div className="hidden sm:block h-16 border-l border-gray-200"></div>
                  <div className="sm:hidden border-t border-gray-100"></div>

                  {/* Total Price */}
                  <div className="flex-1 text-left sm:text-right">
                    <label className="block text-[11px] font-bold text-gray-900 mb-2">
                      Total
                    </label>
                    <span className="text-xl font-bold text-blue-600">
                      {product.currency_symbol || "$"}{(activePrice * quantity).toFixed(2)}
                    </span>
                  </div>
                </div>

                {/* Stock Status */}
                <div
                  className={`mt-3 rounded-lg border px-3 py-2 text-xs sm:text-sm flex items-center gap-2 ${
                    isOutOfStock
                      ? "border-red-200 bg-red-50 text-red-700"
                      : "border-green-200 bg-green-50 text-green-700"
                  }`}
                  role="status"
                >
                  <span
                    className={`inline-block h-2 w-2 rounded-full ${
                      isOutOfStock ? "bg-red-500" : "bg-green-500"
                    }`}
                  ></span>
                  {product.has_variations ? (
                    !matchedVariation ? (
                      <span>Select options to view stock availability</span>
                    ) : activeStock > 5 ? (
                      <span>In Stock - Ready to Ship</span>
                    ) : activeStock > 0 ? (
                      <span>Only {activeStock} left in stock</span>
                    ) : (
                      <span className="font-semibold">Out of Stock</span>
                    )
                  ) : activeStock > 5 ? (
                    <span>In Stock - Ready to Ship</span>
                  ) : activeStock > 0 ? (
                    <span>Only {activeStock} left in stock</span>
                  ) : (
                    <span className="font-semibold">Out of Stock</span>
                  )}
                </div>
              </div>

              {/* Add to Cart & Buy Now Buttons */}
              <div className="flex flex-col md:flex-row gap-3">
                <button
                  onClick={handleAddToCart}
                  type="button"
                  disabled={isOutOfStock || (product.has_variations && !matchedVariation)}
                  className={`w-full md:flex-1 h-12 px-4 rounded-lg font-bold text-white shadow-sm hover:shadow-lg transform active:scale-[0.98] transition-all flex items-center justify-center text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 ${
                    isOutOfStock || (product.has_variations && !matchedVariation)
                      ? "opacity-50 cursor-not-allowed bg-gray-400 hover:bg-gray-400 focus:ring-gray-400"
                      : "bg-blue-600 hover:bg-blue-700 focus:ring-blue-500"
                  }`}
                >
                  <span className="flex items-center gap-2">
                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    {isOutOfStock
                      ? "Out of Stock"
                      : product.has_variations && !matchedVariation
                      ? "Select Configuration"
                      : "Add to Cart"}
                  </span>
                </button>

                <button
                  onClick={handleBuyNow}
                  type="button"
                  disabled={isOutOfStock || (product.has_variations && !matchedVariation) || buyNowLoading}
                  className={`w-full md:flex-1 h-12 px-4 rounded-lg font-bold bg-gray-900 text-white shadow-sm hover:shadow-lg hover:bg-black transform active:scale-[0.98] transition-all flex items-center justify-center gap-2 text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 ${
                    isOutOfStock || (product.has_variations && !matchedVariation)
                      ? "opacity-50 cursor-not-allowed"
                      : ""
                  }`}
                >
                  {buyNowLoading ? (
                    <span className="flex items-center gap-2">
                      <svg className="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
                        <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      <span>Processing...</span>
                    </span>
                  ) : (
                    <>
                      <span>Buy Now</span>
                      <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                      </svg>
                    </>
                  )}
                </button>
              </div>

            </div>

          </div>
        </div>

        {/* Description Section */}
        <section className="bg-white rounded-3xl border border-gray-100 p-6 md:p-8 shadow-sm mb-10">
          <h2 className="text-sm font-black uppercase tracking-wider text-gray-900 mb-3 border-b border-gray-100 pb-2">Description</h2>
          <p className="text-xs text-gray-600 leading-relaxed whitespace-pre-wrap">
            {product.description || "No full description provided for this item."}
          </p>
        </section>

        {/* Customer Reviews Section */}
        <section className="bg-white rounded-3xl border border-gray-100 p-6 md:p-8 shadow-sm mb-10">
          <h2 className="text-sm font-black uppercase tracking-wider text-gray-900 mb-5 border-b border-gray-100 pb-2">
            Reviews ({reviews.length})
          </h2>
          
          {reviews.length > 0 ? (
            <div className="space-y-4">
              {reviews.map((rev) => (
                <div key={rev.id} className="border-b border-gray-55 pb-3 last:border-b-0 last:pb-0">
                  <div className="flex justify-between items-start gap-4">
                    <div>
                      <p className="text-xs font-bold text-gray-800">{rev.customer_name}</p>
                      <div className="flex items-center gap-1 mt-0.5">
                        {Array.from({ length: 5 }).map((_, i) => (
                          <span
                            key={i}
                            className={`text-[10px] ${i < rev.rating ? "text-amber-500" : "text-gray-200"}`}
                          >
                            ★
                          </span>
                        ))}
                      </div>
                    </div>
                    <span className="text-[9px] text-gray-400 font-bold uppercase">{rev.created_at}</span>
                  </div>
                  <p className="text-xs text-gray-500 mt-2 leading-relaxed italic">"{rev.comment}"</p>
                </div>
              ))}
            </div>
          ) : (
            <p className="text-xs text-gray-400 text-center py-4">No reviews yet for this product. Be the first to share your thoughts!</p>
          )}
        </section>

        {/* Related Items Section */}
        {related_products.length > 0 && (
          <section className="space-y-4">
            <h2 className="text-md font-extrabold text-gray-900 tracking-tight pl-1">Related Products</h2>
            <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
              {related_products.map((rp) => {
                const hasSale = rp.sale_price !== null;
                const priceVal = hasSale ? (rp.sale_price as number) : rp.price;
                return (
                  <div
                    key={rp.id}
                    onClick={() => (window.location.href = `/product/${rp.slug}/`)}
                    className="bg-white rounded-2xl border border-gray-100 p-3 hover:shadow-md transition-all duration-300 relative cursor-pointer group flex flex-col justify-between"
                  >
                    <div>
                      <div className="aspect-square bg-gray-50 border border-gray-50 rounded-xl overflow-hidden mb-2 relative">
                        {rp.thumbnail_url ? (
                          <img src={rp.thumbnail_url} alt={rp.name} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                        ) : (
                          <div className="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400 font-bold text-[9px] uppercase">No Image</div>
                        )}
                      </div>
                      <h3 className="text-xs font-bold text-gray-900 line-clamp-2 leading-snug group-hover:text-blue-600 transition-colors">
                        {rp.name}
                      </h3>
                    </div>
                    <div className="flex items-center gap-1.5 mt-2.5">
                      <span className="font-extrabold text-blue-600 text-xs">
                        {rp.currency_symbol || "$"}
                        {priceVal.toFixed(2)}
                      </span>
                    </div>
                  </div>
                );
              })}
            </div>
          </section>
        )}
      </main>

      <Footer />

      {/* Immersive Fullscreen Lightbox Overlay */}
      {isLightboxOpen && (
        <div 
          className="fixed inset-0 z-[100000] bg-black/95 backdrop-blur-xl flex flex-col justify-between p-6 animate-in fade-in duration-300"
          onClick={() => setIsLightboxOpen(false)}
        >
          {/* Top Panel: Title and Close button */}
          <div className="flex items-center justify-between text-white w-full max-w-7xl mx-auto z-10">
            <div className="flex flex-col">
              <span className="text-[10px] font-black uppercase tracking-widest text-blue-400">Viewing Product Image</span>
              <h2 className="text-sm font-bold text-gray-200 line-clamp-1">{product.name}</h2>
            </div>
            <button
              onClick={() => setIsLightboxOpen(false)}
              className="text-gray-400 hover:text-white hover:bg-white/10 p-2.5 rounded-full transition-all active:scale-90"
              aria-label="Close Fullscreen View"
            >
              <svg className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="2.5" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          {/* Middle: Active Image + Arrow controls */}
          <div className="relative flex-grow flex items-center justify-center max-w-7xl w-full mx-auto my-4 min-h-0">
            {images.length > 1 && (
              <button
                type="button"
                onClick={(e) => {
                  e.stopPropagation();
                  handlePrevImage(e);
                }}
                className="absolute left-2 sm:left-4 z-50 text-gray-400 hover:text-white bg-black/30 hover:bg-black/60 border border-white/10 p-3 sm:p-4 rounded-full transition-all active:scale-95 flex items-center justify-center cursor-pointer"
                aria-label="Previous Image"
              >
                <svg className="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" strokeWidth="2.5" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
              </button>
            )}

            <div 
              className="relative max-h-full max-w-full aspect-auto flex items-center justify-center min-h-0 overflow-hidden rounded-2xl bg-black/20 border border-white/5"
              onClick={(e) => e.stopPropagation()}
            >
              <img 
                src={images[lightboxIndex]} 
                alt={product.name} 
                className="max-h-[70vh] sm:max-h-[75vh] w-auto max-w-full object-contain rounded-xl select-none shadow-2xl animate-in zoom-in-95 duration-300"
              />
            </div>

            {images.length > 1 && (
              <button
                type="button"
                onClick={(e) => {
                  e.stopPropagation();
                  handleNextImage(e);
                }}
                className="absolute right-2 sm:right-4 z-50 text-gray-400 hover:text-white bg-black/30 hover:bg-black/60 border border-white/10 p-3 sm:p-4 rounded-full transition-all active:scale-95 flex items-center justify-center cursor-pointer"
                aria-label="Next Image"
              >
                <svg className="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" strokeWidth="2.5" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
              </button>
            )}
          </div>

          {/* Bottom Panel: Gallery thumbnails navigation */}
          <div className="w-full max-w-7xl mx-auto flex flex-col items-center gap-3 z-10" onClick={(e) => e.stopPropagation()}>
            {images.length > 1 && (
              <div className="flex gap-2 overflow-x-auto pb-1 max-w-full no-scrollbar">
                {images.map((img, idx) => (
                  <button
                    key={idx}
                    onClick={() => {
                      setLightboxIndex(idx);
                      setActiveImage(img);
                    }}
                    className={`flex-none h-12 w-16 sm:h-14 sm:w-18 rounded-lg overflow-hidden border-2 bg-neutral-900 transition-all ${
                      lightboxIndex === idx ? "border-blue-500 scale-95" : "border-transparent opacity-50 hover:opacity-100"
                    }`}
                  >
                    <img src={img} alt="Lightbox thumbnail" className="h-full w-full object-cover" />
                  </button>
                ))}
              </div>
            )}
            <span className="text-[10px] font-bold text-gray-500">
              {lightboxIndex + 1} / {images.length}
            </span>
          </div>
        </div>
      )}
    </div>
  );
}
