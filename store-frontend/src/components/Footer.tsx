"use client";

import React from "react";
import Link from "next/link";

interface FooterProps {
  storeName?: string;
  storeSlogan?: string;
  aboutTitle?: string;
  aboutDescription?: string;
  supportEmail?: string;
  supportPhone?: string;
  supportHours1?: string;
  supportHours2?: string;
  facebookUrl?: string;
  xUrl?: string;
  instagramUrl?: string;
}

export default function Footer({
  storeName = "ShopHub",
  storeSlogan = "Curated Products, Fast Delivery",
  aboutTitle,
  aboutDescription = "Your premium destination for curated products, fast delivery, and a seamless storefront experience designed for easy browsing.",
  supportEmail = "support@shophub.com",
  supportPhone = "+1 (555) 019-2834",
  supportHours1 = "Mon - Fri: 9:00 AM - 6:00 PM",
  supportHours2 = "Sat - Sun: 10:00 AM - 4:00 PM",
  facebookUrl = "https://facebook.com",
  xUrl = "https://x.com",
  instagramUrl = "https://instagram.com",
}: FooterProps) {
  const currentYear = new Date().getFullYear();
  const footerTitle = aboutTitle || storeName;

  const quickLinks = [
    { label: "About Us", url: "/about" },
    { label: "Contact Us", url: "/contact" },
    { label: "FAQ", url: "/faq" },
    { label: "Shipping Info", url: "/shipping" },
  ];

  const policyLinks = [
    { label: "Privacy Policy", url: "/privacy-policy" },
    { label: "Terms of Service", url: "/terms" },
    { label: "Cookie Policy", url: "/cookie-policy" },
    { label: "Refund Policy", url: "/refund-policy" },
  ];

  return (
    <footer className="bg-slate-900 text-white mt-12 border-t border-slate-800">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
          {/* About Section */}
          <div className="space-y-4">
            <div className="flex items-center gap-3">
              <div className="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-600 text-white font-black text-sm shadow-md shadow-blue-500/20">
                {storeName.charAt(0).toUpperCase()}
              </div>
              <div className="leading-tight">
                <h3 className="text-md font-bold text-white leading-tight">{footerTitle}</h3>
                <p className="mt-0.5 text-[8px] leading-tight uppercase tracking-[0.2em] text-blue-500">
                  {storeSlogan}
                </p>
              </div>
            </div>
            <p className="text-slate-400 text-xs leading-relaxed">{aboutDescription}</p>

            {/* Social Icons */}
            <div className="flex space-x-3.5 pt-2">
              {facebookUrl && (
                <a
                  href={facebookUrl}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="text-slate-400 hover:text-blue-500 hover:scale-110 active:scale-95 transition-all"
                  aria-label="Facebook"
                >
                  <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M22 12a10 10 0 10-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.52 1.5-3.9 3.8-3.9 1.1 0 2.25.2 2.25.2v2.48H15.2c-1.25 0-1.63.78-1.63 1.57V12h2.78l-.44 2.89h-2.34v6.99A10 10 0 0022 12z" />
                  </svg>
                </a>
              )}
              {xUrl && (
                <a
                  href={xUrl}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="text-slate-400 hover:text-blue-500 hover:scale-110 active:scale-95 transition-all"
                  aria-label="X (Twitter)"
                >
                  <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M18.9 2H22l-6.93 7.92L23.2 22h-6.35l-4.97-6.43L6.23 22H3.1l7.46-8.52L0.8 2h6.52l4.47 5.8L18.9 2zm-1.12 18h1.72L6.42 3.96H4.57L17.78 20z" />
                  </svg>
                </a>
              )}
              {instagramUrl && (
                <a
                  href={instagramUrl}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="text-slate-400 hover:text-blue-500 hover:scale-110 active:scale-95 transition-all"
                  aria-label="Instagram"
                >
                  <svg
                    className="w-5 h-5"
                    fill="none"
                    stroke="currentColor"
                    strokeWidth="2.2"
                    viewBox="0 0 24 24"
                  >
                    <rect x="3" y="3" width="18" height="18" rx="5" ry="5" />
                    <path d="M8 12a4 4 0 118 0 4 4 0 01-8 0z" />
                    <path d="M17.5 6.5h.01" />
                  </svg>
                </a>
              )}
            </div>
          </div>

          {/* Quick Links */}
          <div>
            <h4 className="font-bold text-sm text-slate-100 mb-4 tracking-wide uppercase">
              Quick Links
            </h4>
            <ul className="space-y-2.5 text-xs">
              {quickLinks.map((link) => (
                <li key={link.label}>
                  <Link
                    href={link.url}
                    className="text-slate-400 hover:text-blue-500 hover:translate-x-0.5 transition-all inline-block"
                  >
                    {link.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Customer Support */}
          <div>
            <h4 className="font-bold text-sm text-slate-100 mb-4 tracking-wide uppercase">
              Customer Support
            </h4>
            <ul className="space-y-3 text-xs text-slate-400">
              {supportEmail && (
                <li className="flex items-center gap-2">
                  <span className="text-slate-500 text-sm">📧</span>
                  <a href={`mailto:${supportEmail}`} className="hover:text-blue-500 transition-colors">
                    {supportEmail}
                  </a>
                </li>
              )}
              {supportPhone && (
                <li className="flex items-center gap-2">
                  <span className="text-slate-500 text-sm">📞</span>
                  <a href={`tel:${supportPhone}`} className="hover:text-blue-500 transition-colors">
                    {supportPhone}
                  </a>
                </li>
              )}
              {supportHours1 && (
                <li className="flex items-start gap-2">
                  <span className="text-slate-500 text-sm">🕒</span>
                  <div>
                    <p>{supportHours1}</p>
                    {supportHours2 && <p className="mt-0.5 text-slate-500">{supportHours2}</p>}
                  </div>
                </li>
              )}
            </ul>
          </div>

          {/* Policies */}
          <div>
            <h4 className="font-bold text-sm text-slate-100 mb-4 tracking-wide uppercase">
              Policies
            </h4>
            <ul className="space-y-2.5 text-xs">
              {policyLinks.map((link) => (
                <li key={link.label}>
                  <Link
                    href={link.url}
                    className="text-slate-400 hover:text-blue-500 hover:translate-x-0.5 transition-all inline-block"
                  >
                    {link.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>
        </div>

        {/* Footer Bottom */}
        <div className="border-t border-slate-800 mt-10 pt-8 text-center sm:text-left">
          <div className="flex flex-col sm:flex-row justify-between items-center gap-4 text-xs">
            <p className="text-slate-400">
              © {currentYear} {storeName}. All rights reserved.
            </p>
            <p className="text-[10px] text-slate-500 font-medium uppercase tracking-wider">
              Powered by MicroShopHub
            </p>
          </div>
        </div>
      </div>
    </footer>
  );
}
