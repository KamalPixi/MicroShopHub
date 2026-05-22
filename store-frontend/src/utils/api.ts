/**
 * Decoupled Storefront same-origin API fetch helper
 */

async function request<T>(path: string, options: RequestInit = {}): Promise<T> {
  const defaultHeaders = {
    "Content-Type": "application/json",
    "Accept": "application/json",
  };

  const response = await fetch(path, {
    ...options,
    headers: {
      ...defaultHeaders,
      ...options.headers,
    },
    credentials: "include", // Ensures session cookies are sent for Auth & dynamic cart
  });

  if (!response.ok) {
    const errorData = await response.json().catch(() => ({}));
    throw new Error(errorData.message || `Request failed with status ${response.status}`);
  }

  return response.json() as Promise<T>;
}

// ----------------------------------------------------
// Public Storefront Endpoints
// ----------------------------------------------------

export interface HomepageData {
  settings: Record<string, any>;
  banners: Array<{ image_url: string; link_url: string; alt: string }>;
  categories: Array<{ id: number; name: string; thumbnail_url: string }>;
  featured_products: ProductData[];
  new_arrivals: ProductData[];
  flash_sale: {
    id: number;
    title: string;
    subtitle: string;
    description: string;
    starts_at: string;
    ends_at: string;
  } | null;
  currency: {
    code: string;
    symbol: string;
    exchange_rate: number;
  };
}

export interface ProductData {
  id: number;
  name: string;
  slug: string;
  sku: string;
  description: string;
  price: number;
  sale_price: number | null;
  discount_amount: number;
  discount_percentage: number;
  stock: number;
  has_variations: boolean;
  featured: boolean;
  status: boolean;
  thumbnail_url: string;
  image_urls: string[];
  average_rating: number;
  review_count: number;
  currency_symbol: string;
}

export interface CategoryTree {
  id: number;
  name: string;
  thumbnail_url: string;
  children: Array<{ id: number; name: string; thumbnail_url: string }>;
}

export interface SearchResponse {
  products: ProductData[];
  pagination: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  categories: Array<{
    id: number;
    name: string;
    children: Array<{ id: number; name: string }>;
  }>;
}

export interface ProductDetailResponse {
  product: ProductData;
  options: Array<{
    id: number;
    name: string;
    values: Array<{ id: number; value: string }>;
  }>;
  variations: Array<{
    id: number;
    sku: string;
    price: number;
    sale_price: number | null;
    stock: number;
    value_ids: number[];
  }>;
  related_products: ProductData[];
  reviews: Array<{
    id: number;
    customer_name: string;
    rating: number;
    comment: string;
    created_at: string;
  }>;
  flash_sale: {
    id: number;
    title: string;
    subtitle: string;
    starts_at: string;
    ends_at: string;
  } | null;
}

export interface ResolvedCartItem {
  product_id: number;
  variation_id: number | null;
  name: string;
  slug: string;
  variation_name: string;
  quantity: number;
  price: number;
  original_price: number;
  currency_symbol: string;
  thumbnail_url: string;
  stock: number;
  in_stock: boolean;
  selected_attributes: Record<string, string>;
}

export interface AuthSettings {
  email_otp_enabled: boolean;
  email_password_enabled: boolean;
  guest_checkout_enabled: boolean;
}

export interface UserResponse {
  user: {
    id: number;
    name: string;
    email: string;
    phone: string | null;
  };
  addresses: AddressData[];
  orders: OrderHistoryItem[];
}

export interface AddressData {
  id: number;
  name: string;
  address_line1: string;
  address_line2: string | null;
  city: string;
  state: string | null;
  postal_code: string | null;
  country_code: string;
}

export interface OrderHistoryItem {
  id: number;
  order_number: string;
  status: string;
  payment_status: string;
  payment_method: string;
  subtotal: number;
  discount: number;
  shipping_cost: number;
  total: number;
  currency_code: string;
  created_at: string;
  created_at_humans: string;
  items: Array<{
    name: string;
    price: number;
    quantity: number;
    attributes: Record<string, string>;
  }>;
  shipping_address: any;
}

export interface CheckoutConfigResponse {
  shipping_methods: Array<{
    id: number;
    name: string;
    cost: number;
    description: string;
  }>;
  supported_countries: Array<{ code: string; name: string }>;
  cod_enabled: boolean;
  offline_payment_methods: Array<{
    id: number;
    name: string;
    instructions: string;
  }>;
  saved_addresses: AddressData[];
}

export const api = {
  // Public Storefront
  fetchHomepage: () => request<HomepageData>("/api/homepage"),
  
  fetchCategories: () => request<CategoryTree[]>("/api/categories"),
  
  searchProducts: (params: {
    query?: string;
    category?: number | string;
    min_price?: string;
    max_price?: string;
    sort?: string;
    page?: number;
  }) => {
    const q = new URLSearchParams();
    if (params.query) q.append("query", params.query);
    if (params.category) q.append("category", String(params.category));
    if (params.min_price) q.append("min_price", params.min_price);
    if (params.max_price) q.append("max_price", params.max_price);
    if (params.sort) q.append("sort", params.sort);
    if (params.page) q.append("page", String(params.page));
    return request<SearchResponse>(`/api/products?${q.toString()}`);
  },
  
  fetchProductDetail: (slug: string) => request<ProductDetailResponse>(`/api/products/${slug}`),
  
  fetchFlashSale: () => request<{ flash_sale: any; products: ProductData[] }>("/api/flash-sales"),
  
  resolveCart: (items: Array<{ product_id: number; variation_id: number | null; quantity: number; selected_attributes?: any }>) =>
    request<ResolvedCartItem[]>("/api/cart/resolve", {
      method: "POST",
      body: JSON.stringify({ items }),
    }),
  
  subscribeNewsletter: (email: string) =>
    request<{ message: string }>("/api/newsletter/subscribe", {
      method: "POST",
      body: JSON.stringify({ email }),
    }),
  
  fetchStaticPage: (slug: string) => request<{ title: string; content: string }>(`/api/pages/${slug}`),

  // Authentication & Session
  fetchAuthSettings: () => request<AuthSettings>("/api/auth/settings"),
  
  sendOtp: (email: string) =>
    request<{ message: string }>("/api/auth/send-otp", {
      method: "POST",
      body: JSON.stringify({ email }),
    }),
  
  loginOtp: (email: string, otp: string) =>
    request<{ message: string; user: any }>("/api/auth/login-otp", {
      method: "POST",
      body: JSON.stringify({ email, otp }),
    }),
  
  loginPassword: (email: string, password: string, remember: boolean = false) =>
    request<{ message: string; user: any }>("/api/auth/login-password", {
      method: "POST",
      body: JSON.stringify({ email, password, remember }),
    }),
  
  registerCustomer: (name: string, email: string, password: string) =>
    request<{ message: string; user: any }>("/api/auth/register", {
      method: "POST",
      body: JSON.stringify({ name, email, password }),
    }),
  
  logoutCustomer: () =>
    request<{ message: string }>("/api/auth/logout", {
      method: "POST",
    }),
  
  fetchCurrentUser: () => request<UserResponse>("/api/auth/user"),
  
  validateCoupon: (code: string, subtotal: number) =>
    request<{
      valid: boolean;
      coupon_id: number;
      code: string;
      type: string;
      value: number;
      discount_amount: number;
    }>("/api/coupon/validate", {
      method: "POST",
      body: JSON.stringify({ code, subtotal }),
    }),
  
  fetchCheckoutConfig: () => request<CheckoutConfigResponse>("/api/checkout/config"),
  
  placeOrder: (orderData: {
    email: string;
    phone?: string;
    billing: {
      name: string;
      address_line1: string;
      address_line2?: string;
      city: string;
      state?: string;
      postal_code?: string;
      country_code: string;
    };
    ship_to_different_address: boolean;
    shipping?: {
      name: string;
      address_line1: string;
      address_line2?: string;
      city: string;
      state?: string;
      postal_code?: string;
      country_code: string;
    };
    shipping_method_id: number;
    payment_method: "cod" | "offline";
    offline_payment_method_id?: number;
    offline_reference?: string;
    offline_proof?: string; // base64 string
    coupon_code?: string;
    cart: Array<{ product_id: number; variation_id: number | null; quantity: number; attributes?: any }>;
  }) =>
    request<{ message: string; order_number: string; total: number }>("/api/orders", {
      method: "POST",
      body: JSON.stringify(orderData),
    }),
};
