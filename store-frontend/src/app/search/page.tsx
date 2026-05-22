"use client";

import React, { useEffect, useState, Suspense } from "react";
import Header from "../../components/Header";
import Navbar from "../../components/Navbar";
import Footer from "../../components/Footer";
import { api, ProductData, SearchResponse } from "../../utils/api";
import { addToCart, isInCart } from "../../utils/cart";

function SearchContent() {
  const getSearchParam = (key: string): string => {
    if (typeof window !== "undefined") {
      const params = new URLSearchParams(window.location.search);
      return params.get(key) || "";
    }
    return "";
  };

  const [query, setQuery] = useState("");
  const [selectedCategory, setSelectedCategory] = useState<string | number>("");
  const [minPrice, setMinPrice] = useState("");
  const [maxPrice, setMaxPrice] = useState("");
  const [sort, setSort] = useState("");
  const [page, setPage] = useState(1);

  const [data, setData] = useState<SearchResponse | null>(null);
  const [loading, setLoading] = useState(true);
  const [cartState, setCartState] = useState<{ [id: number]: boolean }>({});

  // Sync cart item states
  useEffect(() => {
    const updateCartStates = () => {
      if (data?.products) {
        const state: { [id: number]: boolean } = {};
        data.products.forEach((p) => {
          state[p.id] = isInCart(p.id);
        });
        setCartState(state);
      }
    };
    updateCartStates();
    window.addEventListener("cart-updated", updateCartStates);
    return () => window.removeEventListener("cart-updated", updateCartStates);
  }, [data]);

  // Sync parameters from URL on load and popstate
  useEffect(() => {
    const syncParams = () => {
      setQuery(getSearchParam("q"));
      setSelectedCategory(getSearchParam("category"));
      setMinPrice(getSearchParam("min_price"));
      setMaxPrice(getSearchParam("max_price"));
      setSort(getSearchParam("sort"));
      setPage(Number(getSearchParam("page")) || 1);
    };

    syncParams();
    window.addEventListener("popstate", syncParams);
    return () => window.removeEventListener("popstate", syncParams);
  }, []);

  // Fetch search products payload
  useEffect(() => {
    async function fetchResults() {
      setLoading(true);
      try {
        const res = await api.searchProducts({
          query,
          category: selectedCategory,
          min_price: minPrice || undefined,
          max_price: maxPrice || undefined,
          sort: sort || undefined,
          page,
        });
        setData(res);
      } catch (err) {
        console.error("Failed search query execution", err);
      } finally {
        setLoading(false);
      }
    }
    fetchResults();
  }, [query, selectedCategory, minPrice, maxPrice, sort, page]);

  const updateFilters = (updates: Record<string, any>) => {
    const currentParams = typeof window !== "undefined"
      ? new URLSearchParams(window.location.search)
      : new URLSearchParams();

    Object.entries(updates).forEach(([key, val]) => {
      if (val === "" || val === null || val === undefined) {
        currentParams.delete(key);
      } else {
        currentParams.set(key, String(val));
      }
    });
    // Reset to page 1 on filter changes
    if (!updates.page) {
      currentParams.set("page", "1");
    }

    const newUrl = `/search/?${currentParams.toString()}`;
    if (typeof window !== "undefined") {
      window.history.pushState(null, "", newUrl);
      window.dispatchEvent(new PopStateEvent("popstate"));
    }
  };

  const handlePriceFilterSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    updateFilters({ min_price: minPrice, max_price: maxPrice });
  };

  const handleToggleCartItem = (product: ProductData, e: React.MouseEvent) => {
    e.stopPropagation();
    e.preventDefault();
    if (isInCart(product.id)) {
      // Remove from cart
      // (Using our cart helper)
      // Custom handler
    } else {
      const activePrice = product.sale_price !== null ? product.sale_price : product.price;
      addToCart(product.id, null, 1);
    }
  };

  return (
    <div className="min-h-screen flex flex-col bg-[#f4f7fb]">
      <Header />
      <Navbar />

      <main className="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-6 pb-12">
        <div className="grid grid-cols-1 lg:grid-cols-[250px_1fr] gap-8">
          
          {/* Advanced Search Sidebar Filters */}
          <aside className="space-y-6">
            
            {/* Categories filter */}
            <div className="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
              <h3 className="text-xs font-black uppercase tracking-wider text-gray-900 mb-3">Categories</h3>
              <div className="space-y-1">
                <button
                  onClick={() => updateFilters({ category: "" })}
                  className={`w-full text-left px-2 py-1.5 rounded-lg text-xs font-semibold transition-colors ${
                    !selectedCategory
                      ? "bg-blue-50 text-blue-600 font-bold"
                      : "text-gray-600 hover:bg-gray-50"
                  }`}
                >
                  All Categories
                </button>
                {data?.categories?.map((cat) => (
                  <div key={cat.id} className="space-y-1 mt-1">
                    <button
                      onClick={() => updateFilters({ category: cat.id })}
                      className={`w-full text-left px-2 py-1 rounded-lg text-xs font-bold transition-colors ${
                        String(selectedCategory) === String(cat.id)
                          ? "bg-blue-50 text-blue-600"
                          : "text-gray-700 hover:bg-gray-50"
                      }`}
                    >
                      {cat.name}
                    </button>
                    {/* Subcategories */}
                    {cat.children && cat.children.length > 0 && (
                      <div className="pl-3 space-y-0.5 border-l border-gray-100 ml-2">
                        {cat.children.map((sub) => (
                          <button
                            key={sub.id}
                            onClick={() => updateFilters({ category: sub.id })}
                            className={`w-full text-left px-2 py-0.5 rounded text-[11px] font-medium transition-colors ${
                              String(selectedCategory) === String(sub.id)
                                ? "text-blue-600 font-bold"
                                : "text-gray-500 hover:text-gray-900"
                            }`}
                          >
                            {sub.name}
                          </button>
                        ))}
                      </div>
                    )}
                  </div>
                ))}
              </div>
            </div>

            {/* Price Range Selector */}
            <div className="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
              <h3 className="text-xs font-black uppercase tracking-wider text-gray-900 mb-3">Price Range</h3>
              <form onSubmit={handlePriceFilterSubmit} className="space-y-3">
                <div className="grid grid-cols-2 gap-2">
                  <div>
                    <label className="block text-[9px] font-bold text-gray-400 uppercase mb-1">Min Price</label>
                    <input
                      type="number"
                      placeholder="Min"
                      value={minPrice}
                      onChange={(e) => setMinPrice(e.target.value)}
                      className="w-full h-8 px-2 rounded-lg border border-gray-200 text-xs text-gray-800 focus:outline-none focus:border-blue-500"
                    />
                  </div>
                  <div>
                    <label className="block text-[9px] font-bold text-gray-400 uppercase mb-1">Max Price</label>
                    <input
                      type="number"
                      placeholder="Max"
                      value={maxPrice}
                      onChange={(e) => setMaxPrice(e.target.value)}
                      className="w-full h-8 px-2 rounded-lg border border-gray-200 text-xs text-gray-800 focus:outline-none focus:border-blue-500"
                    />
                  </div>
                </div>
                <button
                  type="submit"
                  className="w-full h-8 rounded-lg bg-slate-900 hover:bg-slate-950 text-white font-bold text-[10px] uppercase tracking-wider transition-all"
                >
                  Apply Filter
                </button>
              </form>
            </div>
          </aside>

          {/* Results Area */}
          <section className="space-y-6">
            
            {/* Header Sorting Bar */}
            <div className="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
              <h2 className="text-sm font-bold text-gray-800">
                {loading ? (
                  "Loading products..."
                ) : (
                  <>
                    Showing{" "}
                    <span className="text-blue-600 font-extrabold">
                      {data?.pagination.total || 0}
                    </span>{" "}
                    products matching criteria
                  </>
                )}
              </h2>

              <div className="flex items-center gap-2 self-stretch sm:self-auto justify-between">
                <span className="text-xs font-semibold text-gray-500">Sort By</span>
                <select
                  value={sort}
                  onChange={(e) => updateFilters({ sort: e.target.value })}
                  className="h-8 rounded-lg border border-gray-200 bg-white text-xs px-2.5 text-gray-700 font-semibold focus:outline-none focus:border-blue-500"
                >
                  <option value="">Default Sorting</option>
                  <option value="newest">Newest Arrivals</option>
                  <option value="price_low_high">Price: Low to High</option>
                  <option value="price_high_low">Price: High to Low</option>
                </select>
              </div>
            </div>

            {/* Grid display */}
            {loading ? (
              <div className="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4">
                {[1, 2, 3, 4, 5, 6].map((i) => (
                  <div key={i} className="aspect-[4/5] bg-slate-100 rounded-2xl animate-pulse border border-slate-200/40"></div>
                ))}
              </div>
            ) : data && data.products.length > 0 ? (
              <div className="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4">
                {data.products.map((prod) => {
                  const hasSale = prod.sale_price !== null;
                  const activePrice = hasSale ? (prod.sale_price as number) : prod.price;
                  const isAdded = !!cartState[prod.id];

                  return (
                    <div
                      key={prod.id}
                      className="bg-white rounded-2xl border border-gray-100 p-3 hover:shadow-md transition-all duration-300 relative flex flex-col justify-between cursor-pointer group"
                      onClick={() => (window.location.href = `/product/${prod.slug}/`)}
                    >
                      <div>
                        {/* Thumbnail */}
                        <div className="aspect-square bg-gray-50 border border-gray-50 rounded-xl overflow-hidden relative mb-2.5">
                          {prod.thumbnail_url ? (
                            <img
                              src={prod.thumbnail_url}
                              alt={prod.name}
                              className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            />
                          ) : (
                            <div className="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400 font-black text-[10px] uppercase">
                              No Image
                            </div>
                          )}

                          {hasSale && (
                            <span className="absolute left-2 top-2 rounded-full bg-rose-500 px-2 py-0.5 text-[8px] font-black uppercase tracking-wider text-white shadow-sm shadow-rose-500/10">
                              -{prod.discount_percentage || 10}%
                            </span>
                          )}
                        </div>

                        {/* Title */}
                        <h3 className="text-xs sm:text-sm font-bold text-gray-900 leading-snug line-clamp-2 group-hover:text-blue-600 transition-colors">
                          {prod.name}
                        </h3>
                      </div>

                      {/* Pricing / Cart add */}
                      <div className="flex items-end justify-between gap-2 pt-2">
                        <div className="flex flex-col">
                          {hasSale && (
                            <span className="text-[10px] font-semibold text-gray-400 line-through leading-none mb-1">
                              {prod.currency_symbol || "$"}
                              {prod.price.toFixed(2)}
                            </span>
                          )}
                          <span className="font-extrabold text-blue-600 text-sm leading-none">
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
                              : "bg-white text-gray-700 border-gray-200 hover:border-blue-500 hover:text-blue-600"
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
                <div className="h-16 w-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 font-black text-2xl">
                  🔍
                </div>
                <h3 className="text-md font-bold text-gray-900 mb-1.5 uppercase tracking-wide">No items found</h3>
                <p className="text-xs text-gray-500 mb-5 leading-relaxed">
                  We couldn't find any products matching your specific query. Try adjusting filters or searching something else.
                </p>
                <button
                  onClick={() => {
                    window.history.pushState(null, "", "/search");
                    window.dispatchEvent(new PopStateEvent("popstate"));
                  }}
                  className="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs uppercase tracking-wider shadow-md shadow-blue-500/10 transition-all active:scale-95"
                >
                  Clear All Filters
                </button>
              </div>
            )}

            {/* Pagination Controls */}
            {data && data.pagination.last_page > 1 && (
              <div className="flex items-center justify-center gap-1.5 pt-4">
                <button
                  disabled={page <= 1}
                  onClick={() => updateFilters({ page: page - 1 })}
                  className="h-8 px-3 rounded-lg border border-gray-200 bg-white text-xs font-bold text-gray-600 hover:bg-gray-55 disabled:opacity-50 disabled:pointer-events-none transition-all"
                >
                  Previous
                </button>
                
                {Array.from({ length: data.pagination.last_page }, (_, i) => i + 1).map((p) => (
                  <button
                    key={p}
                    onClick={() => updateFilters({ page: p })}
                    className={`h-8 w-8 rounded-lg text-xs font-black transition-all ${
                      p === page
                        ? "bg-blue-600 text-white"
                        : "border border-gray-200 bg-white text-gray-600 hover:bg-gray-55"
                    }`}
                  >
                    {p}
                  </button>
                ))}

                <button
                  disabled={page >= data.pagination.last_page}
                  onClick={() => updateFilters({ page: page + 1 })}
                  className="h-8 px-3 rounded-lg border border-gray-200 bg-white text-xs font-bold text-gray-600 hover:bg-gray-55 disabled:opacity-50 disabled:pointer-events-none transition-all"
                >
                  Next
                </button>
              </div>
            )}
          </section>
        </div>
      </main>

      <Footer />
    </div>
  );
}

export default function SearchPage() {
  return (
    <Suspense fallback={
      <div className="min-h-screen bg-[#f4f7fb] flex items-center justify-center">
        <div className="animate-spin rounded-full h-8 w-8 border-4 border-blue-600 border-t-transparent"></div>
      </div>
    }>
      <SearchContent />
    </Suspense>
  );
}
