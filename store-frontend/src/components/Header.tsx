"use client";

import React, { useState, useEffect, useRef } from "react";
import Link from "next/link";
import { api, CategoryTree, ProductData } from "../utils/api";
import { getCartCount } from "../utils/cart";
import AuthModal from "./AuthModal";
import { useModal } from "@/context/ModalContext";

interface HeaderProps {
  storeName?: string;
  storeSlogan?: string;
  cartCount?: number;
}

export default function Header({
  storeName = "ShopHub",
  storeSlogan = "Curated Products, Fast Delivery",
  cartCount: propCartCount,
}: HeaderProps) {
  const [dynamicStoreName, setDynamicStoreName] = useState(storeName);
  const [dynamicStoreSlogan, setDynamicStoreSlogan] = useState(storeSlogan);

  useEffect(() => {
    setDynamicStoreName(storeName);
  }, [storeName]);

  useEffect(() => {
    setDynamicStoreSlogan(storeSlogan);
  }, [storeSlogan]);

  useEffect(() => {
    if (storeName === "ShopHub" || !storeName) {
      api.fetchHomepage()
        .then((res) => {
          if (res && res.settings) {
            if (res.settings.store_name) setDynamicStoreName(res.settings.store_name);
            if (res.settings.store_slogan) setDynamicStoreSlogan(res.settings.store_slogan);
          }
        })
        .catch((err) => {
          console.error("Failed to load storefront settings in Header", err);
        });
    }
  }, [storeName]);

  const [searchQuery, setSearchQuery] = useState("");
  const [categories, setCategories] = useState<CategoryTree[]>([]);
  const [selectedCategory, setSelectedCategory] = useState("");
  const [searchResults, setSearchResults] = useState<ProductData[]>([]);
  const [searchLoading, setSearchLoading] = useState(false);
  const [showDropdown, setShowDropdown] = useState(false);
  const [cartCountState, setCartCountState] = useState(0);
  const [user, setUser] = useState<any>(null);
  const [profileDropdownOpen, setProfileDropdownOpen] = useState(false);
  const { openModal, closeModal } = useModal();
  const searchRef = useRef<HTMLDivElement>(null);

  // Sync cart count
  useEffect(() => {
    const updateCount = () => {
      setCartCountState(getCartCount());
    };

    updateCount();
    window.addEventListener("cart-updated", updateCount);
    return () => {
      window.removeEventListener("cart-updated", updateCount);
    };
  }, []);

  // Fetch categories for search dropdown
  useEffect(() => {
    api.fetchCategories()
      .then((res) => {
        setCategories(res);
      })
      .catch((err) => {
        console.error("Failed to fetch categories in header", err);
      });
  }, []);

  // Debounced realtime product search
  useEffect(() => {
    if (searchQuery.trim().length < 2) {
      setSearchResults([]);
      setSearchLoading(false);
      return;
    }

    setSearchLoading(true);
    const delayDebounceFn = setTimeout(() => {
      api.searchProducts({
        query: searchQuery,
        category: selectedCategory || undefined,
      })
        .then((res) => {
          setSearchResults(res.products || []);
          setShowDropdown(true);
        })
        .catch((err) => {
          console.error("Realtime search error:", err);
        })
        .finally(() => {
          setSearchLoading(false);
        });
    }, 300);

    return () => clearTimeout(delayDebounceFn);
  }, [searchQuery, selectedCategory]);

  // Close dropdown on click outside
  useEffect(() => {
    const handleClickOutside = (e: MouseEvent) => {
      if (searchRef.current && !searchRef.current.contains(e.target as Node)) {
        setShowDropdown(false);
      }
    };
    document.addEventListener("mousedown", handleClickOutside);
    return () => document.removeEventListener("mousedown", handleClickOutside);
  }, []);

  const stripHtml = (html: string) => {
    return html ? html.replace(/<[^>]*>/g, "") : "";
  };

  // Fetch current authenticated user
  useEffect(() => {
    api.fetchCurrentUser()
      .then((res) => {
        if (res && res.user) {
          setUser(res.user);
        }
      })
      .catch(() => {
        // Not authenticated
        setUser(null);
      });
  }, []);

  const handleSearchSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (searchQuery.trim() || selectedCategory) {
      const params = new URLSearchParams();
      if (searchQuery.trim()) params.append("q", searchQuery.trim());
      if (selectedCategory) params.append("category", selectedCategory);
      window.location.href = `/search/?${params.toString()}`;
    }
  };

  const handleLogout = async () => {
    try {
      await api.logoutCustomer();
      setUser(null);
      setProfileDropdownOpen(false);
      window.location.reload();
    } catch (e) {
      console.error("Logout failed", e);
    }
  };

  const currentCartCount = propCartCount !== undefined ? propCartCount : cartCountState;

  return (
    <header className="bg-white/95 backdrop-blur-md border-b border-gray-100 sticky top-0 z-50 transition-all duration-300">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between h-16 gap-4">
          {/* Logo Section */}
          <div className="flex items-center flex-shrink-0">
            <Link href="/" className="group transition-transform duration-200 active:scale-95">
              <div className="flex items-center gap-3">
                <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-extrabold shadow-md shadow-blue-500/20 group-hover:shadow-blue-500/35 transition-all">
                  {dynamicStoreName ? dynamicStoreName.charAt(0).toUpperCase() : "S"}
                </div>
                <div>
                  <h1 className="text-lg font-black text-gray-900 leading-none tracking-tight group-hover:text-blue-600 transition-colors">
                    {dynamicStoreName}
                  </h1>
                  <p className="mt-1 text-[8px] font-bold uppercase tracking-[0.18em] leading-none text-blue-600/80">
                    {dynamicStoreSlogan}
                  </p>
                </div>
              </div>
            </Link>
          </div>

          {/* Search Bar */}
          <div className="flex-1 max-w-xl mx-4 sm:mx-8 relative" ref={searchRef}>
            <form onSubmit={handleSearchSubmit} className="flex w-full h-10 border border-gray-200 rounded-full overflow-hidden bg-gray-50/50 focus-within:bg-white focus-within:border-blue-500 focus-within:ring-4 focus-within:ring-blue-500/10 transition-all">
              {/* Category Dropdown (matching old blade logic, hidden on mobile/small) */}
              <div className="relative border-r border-gray-150 bg-gray-100/30 hidden sm:block flex-shrink-0 hover:bg-gray-100/60 transition-colors">
                <select
                  value={selectedCategory}
                  onChange={(e) => setSelectedCategory(e.target.value)}
                  className="appearance-none bg-transparent h-full pl-4 pr-8 text-[10px] font-extrabold uppercase tracking-wider text-gray-500 hover:text-gray-900 focus:outline-none cursor-pointer max-w-[130px] truncate"
                >
                  <option value="">All Categories</option>
                  {categories.map((cat) => (
                    <option key={cat.id} value={cat.id}>
                      {cat.name}
                    </option>
                  ))}
                </select>
                <div className="pointer-events-none absolute inset-y-0 right-2 flex items-center px-1 text-gray-400">
                  <svg className="fill-current h-3 w-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                  </svg>
                </div>
              </div>

              {/* Text Input Block */}
              <div className="flex-1 relative h-full">
                <input
                  type="text"
                  placeholder="Search for items, categories or brands..."
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  onFocus={() => {
                    if (searchQuery.trim().length >= 2) setShowDropdown(true);
                  }}
                  className="w-full h-full pl-10 pr-10 bg-transparent text-sm transition-all focus:outline-none placeholder-gray-400 text-gray-800"
                />
                
                {/* Search Magnifying Glass Icon (Left inside Input) */}
                <div className="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    strokeWidth="2.5"
                    stroke="currentColor"
                    className="w-4 h-4"
                  >
                    <path
                      strokeLinecap="round"
                      strokeLinejoin="round"
                      d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.602 10.602Z"
                    />
                  </svg>
                </div>

                {/* Clear Search Input Button */}
                {searchQuery && (
                  <button
                    type="button"
                    onClick={() => {
                      setSearchQuery("");
                      setSearchResults([]);
                      setShowDropdown(false);
                    }}
                    className="absolute right-10 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors active:scale-90"
                    aria-label="Clear query"
                  >
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                  </button>
                )}

                {/* Search Submit Action Button (Right aligned) */}
                <button
                  type="submit"
                  className="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600 transition-colors active:scale-90"
                  aria-label="Submit Search"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    strokeWidth="3"
                    stroke="currentColor"
                    className="w-4 h-4"
                  >
                    <path strokeLinecap="round" strokeLinejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                  </svg>
                </button>
              </div>
            </form>

            {/* Realtime Autocomplete Search Results */}
            {showDropdown && searchQuery.trim().length >= 2 && (
              <div className="absolute top-full left-0 right-0 z-[100] mt-2 rounded-2xl bg-white border border-gray-100 shadow-2xl overflow-hidden animate-in fade-in slide-in-from-top-2 duration-200">
                {searchLoading ? (
                  <div className="flex items-center justify-center py-8 gap-2">
                    <div className="h-4 w-4 animate-spin rounded-full border-2 border-blue-600 border-t-transparent"></div>
                    <span className="text-xs text-gray-400 font-bold uppercase tracking-wider">Searching products...</span>
                  </div>
                ) : searchResults.length > 0 ? (
                  <>
                    <ul className="divide-y divide-gray-50 max-h-[320px] overflow-y-auto pr-1">
                      {searchResults.map((product) => (
                        <li key={product.id} className="hover:bg-slate-50 transition-colors">
                          <Link
                            href={`/product/${product.slug}`}
                            onClick={() => setShowDropdown(false)}
                            className="flex items-center px-4 py-3 group"
                          >
                            {/* Thumbnail */}
                            <div className="flex-shrink-0 h-10 w-10 border border-gray-150 rounded-lg overflow-hidden bg-slate-50">
                              <img
                                src={product.thumbnail_url || "https://placehold.co/100"}
                                alt={product.name}
                                className="w-full h-full object-cover"
                              />
                            </div>
                            {/* Name and Description snippet */}
                            <div className="ml-3 flex-1 min-w-0">
                              <p className="text-xs font-bold text-gray-900 truncate group-hover:text-blue-600 transition-colors">
                                {product.name}
                              </p>
                              <p className="text-[10px] text-gray-400 truncate mt-0.5">
                                {stripHtml(product.description)}
                              </p>
                            </div>
                            {/* Price block */}
                            <div className="ml-3 text-right flex-shrink-0">
                              {product.sale_price ? (
                                <>
                                  <span className="block text-[9px] text-gray-400 line-through leading-none">
                                    {product.currency_symbol}{product.price.toFixed(2)}
                                  </span>
                                  <span className="text-xs font-black text-blue-600 leading-none mt-1 inline-block">
                                    {product.currency_symbol}{product.sale_price.toFixed(2)}
                                  </span>
                                </>
                              ) : (
                                <span className="text-xs font-bold text-gray-900 leading-none">
                                  {product.currency_symbol}{product.price.toFixed(2)}
                                </span>
                              )}
                            </div>
                          </Link>
                        </li>
                      ))}
                    </ul>
                    
                    <div className="bg-slate-50 p-2 text-center border-t border-gray-100">
                      <button
                        type="submit"
                        onClick={() => setShowDropdown(false)}
                        className="text-blue-600 hover:text-blue-700 text-[10px] font-extrabold uppercase tracking-wider transition-colors w-full py-1.5 hover:underline"
                      >
                        View All Results for "{searchQuery}"
                      </button>
                    </div>
                  </>
                ) : (
                  <div className="px-4 py-6 text-center text-xs text-gray-400 font-bold">
                    🔍 No products found for "{searchQuery}"
                  </div>
                )}
              </div>
            )}
          </div>

          {/* Action Links */}
          <div className="flex items-center gap-3 flex-shrink-0 relative">
            {/* Locale Selector Toggle (Mocked) */}
            <div className="hidden md:flex items-center gap-1 rounded-full border border-gray-100 bg-gray-50 p-1">
              <button className="rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-wider bg-white text-blue-600 shadow-sm transition-all">
                EN
              </button>
              <button className="rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-gray-500 hover:text-gray-900 transition-all">
                BN
              </button>
            </div>

            {/* Profile Action / Dropdown */}
            <div className="relative">
              {user ? (
                <>
                  <button
                    onClick={() => setProfileDropdownOpen(!profileDropdownOpen)}
                    className="inline-flex items-center justify-center h-10 px-3.5 rounded-full border border-blue-100 bg-blue-50/30 text-blue-600 hover:bg-blue-50/60 active:scale-95 transition-all text-xs font-bold gap-1.5"
                    title="Account Options"
                  >
                    <span className="h-5 w-5 rounded-full bg-blue-600 text-white flex items-center justify-center font-black text-[9px] uppercase">
                      {user.name.charAt(0)}
                    </span>
                    <span className="hidden sm:inline line-clamp-1 max-w-[80px]">
                      {user.name.split(" ")[0]}
                    </span>
                  </button>

                  {profileDropdownOpen && (
                    <div className="absolute right-0 mt-2 w-48 bg-white border border-gray-100 shadow-xl rounded-2xl py-2 z-50 animate-in fade-in slide-in-from-top-1 duration-150">
                      <div className="px-4 py-2 border-b border-gray-55">
                        <p className="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Signed in as</p>
                        <p className="text-xs font-bold text-gray-800 line-clamp-1">{user.name}</p>
                      </div>
                      <a
                        href="/dashboard"
                        onClick={(e) => {
                          e.preventDefault();
                          setProfileDropdownOpen(false);
                          window.location.href = "/dashboard";
                        }}
                        className="block px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600 transition-colors"
                      >
                        My Dashboard
                      </a>
                      <Link
                        href="/checkout"
                        onClick={() => setProfileDropdownOpen(false)}
                        className="block px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600 transition-colors"
                      >
                        Checkout Portal
                      </Link>
                      <button
                        onClick={handleLogout}
                        className="w-full text-left block px-4 py-2 text-xs font-medium text-rose-600 hover:bg-rose-50/50 transition-colors border-t border-gray-50 mt-1"
                      >
                        Logout
                      </button>
                    </div>
                  )}
                </>
              ) : (
                <button
                  onClick={() =>
                    openModal(
                      <AuthModal
                        onSuccess={(authenticatedUser) => {
                          setUser(authenticatedUser);
                          window.location.reload();
                        }}
                        onClose={closeModal}
                      />
                    )
                  }
                  className="inline-flex items-center justify-center h-10 w-10 rounded-full border border-gray-100 bg-white text-gray-600 hover:text-blue-600 hover:border-blue-100 hover:bg-blue-50/20 active:scale-95 transition-all"
                  title="Sign In / Register"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    strokeWidth="2"
                    stroke="currentColor"
                    className="w-5 h-5"
                  >
                    <path
                      strokeLinecap="round"
                      strokeLinejoin="round"
                      d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"
                    />
                  </svg>
                </button>
              )}
            </div>

            {/* Cart Button */}
            <Link
              href="/cart"
              className="relative inline-flex items-center justify-center h-10 px-4 rounded-full bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-md shadow-blue-500/10 hover:shadow-blue-500/20 active:scale-95 transition-all gap-2"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                strokeWidth="2.2"
                stroke="currentColor"
                className="w-4 h-4"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"
                />
              </svg>
              <span>Cart</span>
              {currentCartCount > 0 && (
                <span className="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white ring-2 ring-white">
                  {currentCartCount}
                </span>
              )}
            </Link>
          </div>
        </div>
      </div>
    </header>
  );
}
