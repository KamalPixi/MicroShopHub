"use client";

import React, { useState, useEffect } from "react";
import Link from "next/link";
import { api } from "../utils/api";

interface SubCategory {
  id: number;
  name: string;
}

interface Category {
  id: number;
  name: string;
  children?: SubCategory[];
}

interface NavbarProps {
  categories?: Category[];
}

const defaultCategories: Category[] = [
  {
    id: 1,
    name: "Electronics",
    children: [
      { id: 11, name: "Smartphones" },
      { id: 12, name: "Laptops & PCs" },
      { id: 13, name: "Accessories" },
    ],
  },
  {
    id: 2,
    name: "Fashion",
    children: [
      { id: 21, name: "Men's Clothing" },
      { id: 22, name: "Women's Clothing" },
      { id: 23, name: "Footwear" },
    ],
  },
  {
    id: 3,
    name: "Home & Living",
    children: [
      { id: 31, name: "Furniture" },
      { id: 32, name: "Home Decor" },
      { id: 33, name: "Kitchenware" },
    ],
  },
  { id: 4, name: "Fitness & Outdoors" },
  { id: 5, name: "Books & Stationery" },
];

export default function Navbar({ categories: propCategories }: NavbarProps) {
  const [categories, setCategories] = useState<Category[]>([]);
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const [activeDropdown, setActiveDropdown] = useState<number | null>(null);
  const [expandedMobileCategory, setExpandedMobileCategory] = useState<number | null>(null);

  useEffect(() => {
    if (propCategories) {
      setCategories(propCategories);
      return;
    }

    api.fetchCategories()
      .then((res) => {
        if (res && res.length > 0) {
          // Map to match the props structure
          const mapped = res.map((cat) => ({
            id: cat.id,
            name: cat.name,
            children: cat.children?.map((child) => ({
              id: child.id,
              name: child.name,
            })) || [],
          }));
          setCategories(mapped);
        } else {
          setCategories(defaultCategories);
        }
      })
      .catch((err) => {
        console.error("Failed to load categories dynamically", err);
        setCategories(defaultCategories);
      });
  }, [propCategories]);

  const toggleMobileCategory = (id: number) => {
    setExpandedMobileCategory(expandedMobileCategory === id ? null : id);
  };

  return (
    <nav className="bg-white border-b border-gray-150 relative z-30">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="h-12 flex items-center justify-between md:justify-center">
          {/* Mobile Menu Toggle Button */}
          <button
            onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
            type="button"
            className="md:hidden inline-flex items-center justify-center p-2 rounded-lg text-gray-500 hover:text-blue-600 hover:bg-gray-50 focus:outline-none transition-colors"
            aria-label="Open main menu"
          >
            {mobileMenuOpen ? (
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                strokeWidth="2"
                stroke="currentColor"
                className="w-5 h-5"
              >
                <path strokeLinecap="round" strokeLinejoin="round" d="M6 18 18 6M6 6l12 12" />
              </svg>
            ) : (
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                strokeWidth="2"
                stroke="currentColor"
                className="w-5 h-5"
              >
                <path strokeLinecap="round" strokeLinejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
              </svg>
            )}
          </button>

          <div className="md:hidden font-bold text-gray-800 text-sm tracking-wide">
            Categories
          </div>

          {/* Desktop Categories List */}
          <div className="hidden md:flex justify-center space-x-8 items-center h-full">
            <Link
              href="/"
              className="text-gray-600 hover:text-blue-600 font-semibold text-xs tracking-wide uppercase transition-colors"
            >
              All Products
            </Link>

            {categories.map((category) => {
              const hasChildren = category.children && category.children.length > 0;
              return (
                <div
                  key={category.id}
                  className="relative h-full flex items-center"
                  onMouseEnter={() => hasChildren && setActiveDropdown(category.id)}
                  onMouseLeave={() => hasChildren && setActiveDropdown(null)}
                >
                  <div className="flex items-center space-x-1 text-gray-600 hover:text-blue-600 cursor-pointer font-semibold text-xs tracking-wide uppercase transition-colors">
                    <Link href={`/search/?category=${category.id}`} className="hover:text-blue-600">
                      {category.name}
                    </Link>
                    {hasChildren && (
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        strokeWidth="3"
                        stroke="currentColor"
                        className={`w-2.5 h-2.5 transition-transform duration-200 ${
                          activeDropdown === category.id ? "rotate-180" : ""
                        }`}
                      >
                        <path strokeLinecap="round" strokeLinejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                      </svg>
                    )}
                  </div>

                  {/* Desktop Subcategory Dropdown */}
                  {hasChildren && activeDropdown === category.id && (
                    <div className="absolute top-full left-1/2 -translate-x-1/2 w-48 bg-white border border-gray-100 shadow-xl rounded-b-xl py-2 min-w-[200px] animate-in fade-in slide-in-from-top-1 duration-150">
                      {category.children?.map((child) => (
                        <Link
                          key={child.id}
                          href={`/search/?category=${child.id}`}
                          className="block px-4 py-2.5 text-xs font-medium text-gray-700 hover:bg-blue-50/40 hover:text-blue-600 transition-colors"
                        >
                          {child.name}
                        </Link>
                      ))}
                    </div>
                  )}
                </div>
              );
            })}
          </div>
        </div>
      </div>

      {/* Mobile Categories Menu */}
      {mobileMenuOpen && (
        <div className="md:hidden border-t border-gray-100 bg-gray-55 relative z-40 animate-in slide-in-from-top-2 duration-200">
          <div className="px-3 pt-2 pb-4 space-y-1">
            <Link
              href="/"
              onClick={() => setMobileMenuOpen(false)}
              className="block px-3 py-2.5 rounded-lg text-sm font-semibold text-gray-700 hover:text-blue-600 hover:bg-blue-50/20 transition-all"
            >
              All Products
            </Link>

            {categories.map((category) => {
              const hasChildren = category.children && category.children.length > 0;
              const isExpanded = expandedMobileCategory === category.id;

              return (
                <div key={category.id} className="space-y-0.5">
                  <div className="flex justify-between items-center px-3 py-2.5 rounded-lg text-sm font-semibold text-gray-700 hover:text-blue-600 hover:bg-blue-50/20 transition-all">
                    <Link
                      href={`/search/?category=${category.id}`}
                      onClick={() => setMobileMenuOpen(false)}
                      className="hover:text-blue-600"
                    >
                      {category.name}
                    </Link>
                    {hasChildren && (
                      <button
                        type="button"
                        onClick={() => toggleMobileCategory(category.id)}
                        className="p-1 rounded-full text-gray-400 hover:text-blue-600 focus:outline-none transition-colors"
                        aria-label={`Toggle ${category.name} dropdown`}
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          fill="none"
                          viewBox="0 0 24 24"
                          strokeWidth="2.5"
                          stroke="currentColor"
                          className={`w-4 h-4 transition-transform duration-200 ${
                            isExpanded ? "rotate-180" : ""
                          }`}
                        >
                          <path strokeLinecap="round" strokeLinejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                      </button>
                    )}
                  </div>

                  {hasChildren && isExpanded && (
                    <div className="pl-4 space-y-0.5 border-l border-gray-100 ml-5 animate-in slide-in-from-left-2 duration-150">
                      {category.children?.map((child) => (
                        <Link
                          key={child.id}
                          href={`/search/?category=${child.id}`}
                          onClick={() => setMobileMenuOpen(false)}
                          className="block px-3 py-2 rounded-md text-xs font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50/10 transition-all"
                        >
                          {child.name}
                        </Link>
                      ))}
                    </div>
                  )}
                </div>
              );
            })}
          </div>
        </div>
      )}
    </nav>
  );
}
