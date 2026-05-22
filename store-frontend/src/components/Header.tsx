"use client";

import React, { useState, useEffect } from "react";
import Link from "next/link";
import { api } from "../utils/api";
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
  const [searchQuery, setSearchQuery] = useState("");
  const [cartCountState, setCartCountState] = useState(0);
  const [user, setUser] = useState<any>(null);
  const [profileDropdownOpen, setProfileDropdownOpen] = useState(false);
  const { openModal, closeModal } = useModal();

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
    if (searchQuery.trim()) {
      window.location.href = `/search/?q=${encodeURIComponent(searchQuery)}`;
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
                  {storeName.charAt(0).toUpperCase()}
                </div>
                <div>
                  <h1 className="text-lg font-black text-gray-900 leading-none tracking-tight group-hover:text-blue-600 transition-colors">
                    {storeName}
                  </h1>
                  <p className="mt-1 text-[8px] font-bold uppercase tracking-[0.18em] leading-none text-blue-600/80">
                    {storeSlogan}
                  </p>
                </div>
              </div>
            </Link>
          </div>

          {/* Search Bar */}
          <div className="flex-1 max-w-xl mx-4 sm:mx-8">
            <form onSubmit={handleSearchSubmit} className="relative">
              <input
                type="text"
                placeholder="Search for items, categories or brands..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="w-full h-10 pl-4 pr-10 rounded-full border border-gray-200 bg-gray-50/50 text-sm transition-all focus:outline-none focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 placeholder-gray-400 text-gray-800"
              />
              <button
                type="submit"
                className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600 transition-colors"
                aria-label="Search"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  strokeWidth="2.5"
                  stroke="currentColor"
                  className="w-5 h-5"
                >
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.602 10.602Z"
                  />
                </svg>
              </button>
            </form>
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
