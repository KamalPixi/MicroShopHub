import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  output: "export",
  trailingSlash: true,
  images: {
    unoptimized: true, // Necessary for static export since Next.js image optimization relies on a running Node server
  },
};

export default nextConfig;
