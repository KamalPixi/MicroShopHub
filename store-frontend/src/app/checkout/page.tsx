"use client";

import React, { useEffect, useState } from "react";
import Header from "../../components/Header";
import Navbar from "../../components/Navbar";
import Footer from "../../components/Footer";
import { api, AddressData, CheckoutConfigResponse, ResolvedCartItem } from "../../utils/api";
import { getCart, clearCart } from "../../utils/cart";
import AuthModal from "../../components/AuthModal";
import { useModal } from "@/context/ModalContext";

export default function CheckoutPage() {
  const [cartItems, setCartItems] = useState(getCart());
  const [resolvedCart, setResolvedCart] = useState<ResolvedCartItem[]>([]);
  const [config, setConfig] = useState<CheckoutConfigResponse | null>(null);
  
  // Auth state
  const [user, setUser] = useState<any>(null);
  const [guestCheckoutAllowed, setGuestCheckoutAllowed] = useState(false);
  const [checkoutAsGuest, setCheckoutAsGuest] = useState(false);
  const { openModal, closeModal } = useModal();
  
  // Checkout Form State
  const [email, setEmail] = useState("");
  const [phone, setPhone] = useState("");
  const [billingName, setBillingName] = useState("");
  const [billingAddress1, setBillingAddress1] = useState("");
  const [billingAddress2, setBillingAddress2] = useState("");
  const [billingCity, setBillingCity] = useState("");
  const [billingState, setBillingState] = useState("");
  const [billingPostalCode, setBillingPostalCode] = useState("");
  const [billingCountry, setBillingCountry] = useState("");

  const [shipToDifferent, setShipToDifferent] = useState(false);
  const [shippingName, setShippingName] = useState("");
  const [shippingAddress1, setShippingAddress1] = useState("");
  const [shippingAddress2, setShippingAddress2] = useState("");
  const [shippingCity, setShippingCity] = useState("");
  const [shippingState, setShippingState] = useState("");
  const [shippingPostalCode, setShippingPostalCode] = useState("");
  const [shippingCountry, setShippingCountry] = useState("");

  const [selectedAddressId, setSelectedAddressId] = useState<number | null>(null);
  const [selectedShippingMethodId, setSelectedShippingMethodId] = useState<number | null>(null);
  const [paymentMethod, setPaymentMethod] = useState<"cod" | "offline">("cod");
  const [offlineMethodId, setOfflineMethodId] = useState<number | null>(null);
  const [offlineReference, setOfflineReference] = useState("");
  const [offlineProofBase64, setOfflineProofBase64] = useState("");

  // Coupon State
  const [couponCode, setCouponCode] = useState("");
  const [couponLoading, setCouponLoading] = useState(false);
  const [couponError, setCouponError] = useState("");
  const [appliedCoupon, setAppliedCoupon] = useState<any>(null);

  // Status State
  const [pageLoading, setPageLoading] = useState(true);
  const [submitLoading, setSubmitLoading] = useState(false);
  const [orderSuccess, setOrderSuccess] = useState<any>(null);
  const [orderError, setOrderError] = useState("");

  // Check Auth & Retrieve Configurations
  useEffect(() => {
    async function initCheckout() {
      try {
        // 1. Fetch user auth status
        try {
          const uRes = await api.fetchCurrentUser();
          setUser(uRes.user);
          setEmail(uRes.user.email);
          setPhone(uRes.user.phone || "");
        } catch {
          // Not logged in
          setUser(null);
        }

        // 2. Fetch auth rules
        try {
          const authSet = await api.fetchAuthSettings();
          setGuestCheckoutAllowed(authSet.guest_checkout_enabled);
        } catch {
          setGuestCheckoutAllowed(true);
        }

        // 3. Fetch checkout dynamics
        const cRes = await api.fetchCheckoutConfig();
        setConfig(cRes);
        if (cRes.shipping_methods.length > 0) {
          setSelectedShippingMethodId(cRes.shipping_methods[0].id);
        }
        if (cRes.saved_addresses.length > 0) {
          setSelectedAddressId(cRes.saved_addresses[0].id);
          applySavedAddress(cRes.saved_addresses[0]);
        }
        if (cRes.supported_countries.length > 0) {
          setBillingCountry(cRes.supported_countries[0].code);
          setShippingCountry(cRes.supported_countries[0].code);
        }
        if (cRes.offline_payment_methods.length > 0) {
          setOfflineMethodId(cRes.offline_payment_methods[0].id);
        }

        // 4. Resolve active cart totals
        const items = getCart();
        if (items.length > 0) {
          const rRes = await api.resolveCart(
            items.map((i) => ({
              product_id: i.product_id,
              variation_id: i.variation_id,
              quantity: i.quantity,
            }))
          );
          setResolvedCart(rRes);
        }
      } catch (err) {
        console.error("Failed to initialize checkout configs", err);
      } finally {
        setPageLoading(false);
      }
    }
    initCheckout();
  }, []);

  const applySavedAddress = (addr: AddressData) => {
    setBillingName(addr.name);
    setBillingAddress1(addr.address_line1);
    setBillingAddress2(addr.address_line2 || "");
    setBillingCity(addr.city);
    setBillingState(addr.state || "");
    setBillingPostalCode(addr.postal_code || "");
    setBillingCountry(addr.country_code);
  };

  const handleSavedAddressChange = (id: number) => {
    setSelectedAddressId(id);
    const addr = config?.saved_addresses.find((a) => a.id === id);
    if (addr) {
      applySavedAddress(addr);
    }
  };

  // Validate coupon
  const handleApplyCoupon = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!couponCode.trim()) return;

    setCouponLoading(true);
    setCouponError("");
    try {
      const res = await api.validateCoupon(couponCode, cartSubtotal);
      if (res.valid) {
        setAppliedCoupon(res);
      } else {
        setCouponError("Invalid or expired coupon code.");
        setAppliedCoupon(null);
      }
    } catch (err: any) {
      setCouponError(err.message || "Failed to validate coupon code.");
      setAppliedCoupon(null);
    } finally {
      setCouponLoading(false);
    }
  };

  const handleRemoveCoupon = () => {
    setAppliedCoupon(null);
    setCouponCode("");
    setCouponError("");
  };

  // Upload dynamic receipt image to base64
  const handleReceiptUpload = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onloadend = () => {
      setOfflineProofBase64(reader.result as string);
    };
    reader.readAsDataURL(file);
  };

  // Place secure Order
  const handleSubmitOrder = async (e: React.FormEvent) => {
    e.preventDefault();
    if (cartItems.length === 0) return;

    setSubmitLoading(true);
    setOrderError("");

    const orderPayload: any = {
      email,
      phone,
      billing: {
        name: billingName,
        address_line1: billingAddress1,
        address_line2: billingAddress2 || undefined,
        city: billingCity,
        state: billingState || undefined,
        postal_code: billingPostalCode || undefined,
        country_code: billingCountry,
      },
      ship_to_different_address: shipToDifferent,
      shipping_method_id: selectedShippingMethodId,
      payment_method: paymentMethod,
      coupon_code: appliedCoupon ? appliedCoupon.code : undefined,
      cart: cartItems.map((i) => ({
        product_id: i.product_id,
        variation_id: i.variation_id,
        quantity: i.quantity,
        attributes: i.selected_attributes,
      })),
    };

    if (shipToDifferent) {
      orderPayload.shipping = {
        name: shippingName,
        address_line1: shippingAddress1,
        address_line2: shippingAddress2 || undefined,
        city: shippingCity,
        state: shippingState || undefined,
        postal_code: shippingPostalCode || undefined,
        country_code: shippingCountry,
      };
    }

    if (paymentMethod === "offline") {
      orderPayload.offline_payment_method_id = offlineMethodId;
      orderPayload.offline_reference = offlineReference || undefined;
      orderPayload.offline_proof = offlineProofBase64 || undefined;
    }

    try {
      const res = await api.placeOrder(orderPayload);
      setOrderSuccess(res);
      clearCart();
    } catch (err: any) {
      setOrderError(err.message || "Failed to submit order. Check inventory stocks.");
    } finally {
      setSubmitLoading(false);
    }
  };

  const cartSubtotal = resolvedCart.reduce((sum, item) => sum + item.price * item.quantity, 0);
  const selectedShipping = config?.shipping_methods.find((m) => m.id === selectedShippingMethodId);
  const shippingCost = selectedShipping ? selectedShipping.cost : 0;
  
  const couponDiscount = appliedCoupon ? appliedCoupon.discount_amount : 0;
  const orderTotal = Math.max(0, cartSubtotal + shippingCost - couponDiscount);
  
  const currencySymbol = resolvedCart[0]?.currency_symbol || "$";

  // Loading Screen
  if (pageLoading) {
    return (
      <div className="min-h-screen bg-[#f4f7fb] flex items-center justify-center">
        <div className="animate-spin rounded-full h-8 w-8 border-4 border-blue-600 border-t-transparent"></div>
      </div>
    );
  }

  // Order Success Screen
  if (orderSuccess) {
    return (
      <div className="min-h-screen flex flex-col bg-[#f4f7fb]">
        <Header />
        <Navbar />
        <main className="flex-grow max-w-xl mx-auto px-4 w-full pt-16 pb-12 text-center animate-in zoom-in-95 duration-300">
          <div className="h-20 w-20 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6 font-black text-4xl shadow-md shadow-emerald-500/10">
            ✓
          </div>
          <h1 className="text-xl md:text-2xl font-black text-gray-900 uppercase tracking-tight mb-2">Order Confirmed!</h1>
          <p className="text-xs text-gray-500 mb-6 leading-relaxed">
            Thank you for shopping with us. Your dynamic order has been processed securely and registered in our database.
          </p>

          <div className="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm text-left space-y-3 mb-6">
            <div className="flex justify-between items-center text-xs text-gray-500">
              <span>Order Number</span>
              <span className="font-extrabold text-gray-900">{orderSuccess.order_number}</span>
            </div>
            <div className="flex justify-between items-center text-xs text-gray-500">
              <span>Total Paid</span>
              <span className="font-extrabold text-blue-600">
                {currencySymbol}
                {orderSuccess.total.toFixed(2)}
              </span>
            </div>
            <div className="flex justify-between items-center text-xs text-gray-500">
              <span>Payment Type</span>
              <span className="font-extrabold text-gray-800 uppercase">{paymentMethod}</span>
            </div>
          </div>

          <a
            href="/"
            className="w-full h-11 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs uppercase tracking-wider flex items-center justify-center transition-all active:scale-95 shadow-md shadow-blue-500/15"
          >
            Continue Shopping
          </a>
        </main>
        <Footer />
      </div>
    );
  }

  // Empty Cart block
  if (cartItems.length === 0) {
    return (
      <div className="min-h-screen flex flex-col bg-[#f4f7fb]">
        <Header />
        <Navbar />
        <main className="flex-grow max-w-md mx-auto px-4 w-full pt-16 pb-12 text-center">
          <div className="h-16 w-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 font-black text-2xl">
            🛒
          </div>
          <h3 className="text-md font-bold text-gray-900 mb-1.5 uppercase tracking-wide">Empty Checkout</h3>
          <p className="text-xs text-gray-500 mb-5 leading-relaxed">
            Your shopping cart is empty. You must add items to your cart before proceeding to the checkout portal.
          </p>
          <a
            href="/"
            className="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs uppercase tracking-wider shadow-md inline-block transition-all"
          >
            Return Home
          </a>
        </main>
        <Footer />
      </div>
    );
  }

  // Force login if guest checkout disabled and user is not authenticated
  if (!user && !guestCheckoutAllowed && !checkoutAsGuest) {
    return (
      <div className="min-h-screen flex flex-col bg-[#f4f7fb]">
        <Header />
        <Navbar />
        <main className="flex-grow max-w-md mx-auto px-4 w-full pt-16 pb-12 text-center animate-in zoom-in-95 duration-300">
          <div className="h-16 w-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 font-black text-2xl">
            🔒
          </div>
          <h3 className="text-md font-bold text-gray-900 mb-1.5 uppercase tracking-wide">Secure Authentication Required</h3>
          <p className="text-xs text-gray-500 mb-5 leading-relaxed">
            This merchant requires a dynamic verified customer session to submit orders. Sign in to your account to complete checkout.
          </p>
          <div className="space-y-3">
            <button
               onClick={() => openModal(
                 <AuthModal
                   onSuccess={(u) => {
                     setUser(u);
                     setEmail(u.email);
                     setPhone(u.phone || "");
                     window.location.reload();
                   }}
                   onClose={closeModal}
                 />
               )}
              className="w-full h-11 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs uppercase tracking-wider transition-all active:scale-95 shadow-md shadow-blue-500/10"
            >
              Sign In / Register
            </button>
            <a
              href="/cart"
              className="w-full h-11 rounded-xl border border-gray-250 bg-white text-gray-700 font-bold text-xs uppercase tracking-wider flex items-center justify-center transition-all active:scale-95"
            >
              Return to Cart
            </a>
          </div>
        </main>
        <Footer />
      </div>
    );
  }

  return (
    <div className="min-h-screen flex flex-col bg-[#f4f7fb]">
      <Header />
      <Navbar />

      <main className="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-6 pb-12">
        <h1 className="text-xl md:text-2xl font-black text-gray-900 uppercase tracking-tight mb-6">Secure Checkout</h1>

        {/* Dynamic Errors */}
        {orderError && (
          <div className="mb-6 rounded-3xl bg-rose-50 border border-rose-100 p-4 text-xs text-rose-600 font-bold animate-in slide-in-from-bottom-2">
            ⚠️ Order Submission Failure: {orderError}
          </div>
        )}

        <form onSubmit={handleSubmitOrder} className="grid grid-cols-1 lg:grid-cols-[1fr_380px] gap-8 relative">
          
          {/* Submit Loading Overlay */}
          {submitLoading && (
            <div className="absolute inset-0 bg-slate-900/10 backdrop-blur-[1px] flex items-center justify-center z-[80] rounded-3xl">
              <div className="h-10 w-10 animate-spin rounded-full border-4 border-blue-600 border-t-transparent"></div>
            </div>
          )}

          {/* LEFT: Address and Payments Form */}
          <div className="space-y-6">
            
            {/* Guest vs login banner */}
            {!user && guestCheckoutAllowed && !checkoutAsGuest && (
              <div className="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100/50 rounded-3xl p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div className="text-center sm:text-left">
                  <h4 className="text-xs font-black text-blue-900 uppercase tracking-wide">Checkout Options</h4>
                  <p className="text-[10px] text-blue-700/80 mt-0.5">Log in for saved addresses and loyalty benefits, or proceed instantly as guest.</p>
                </div>
                <div className="flex gap-2">
                  <button
                    type="button"
                    onClick={() => openModal(
                      <AuthModal
                        onSuccess={(u) => {
                          setUser(u);
                          setEmail(u.email);
                          setPhone(u.phone || "");
                          window.location.reload();
                        }}
                        onClose={closeModal}
                      />
                    )}
                    className="h-8 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-[10px] uppercase tracking-wider transition-all active:scale-95"
                  >
                    Login
                  </button>
                  <button
                    type="button"
                    onClick={() => setCheckoutAsGuest(true)}
                    className="h-8 px-4 rounded-xl border border-gray-250 bg-white text-gray-700 font-bold text-[10px] uppercase tracking-wider transition-all active:scale-95"
                  >
                    Guest Checkout
                  </button>
                </div>
              </div>
            )}

            {/* Address Form Card */}
            {(user || checkoutAsGuest) && (
              <div className="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm space-y-5">
                <h3 className="text-xs font-black uppercase tracking-wider text-gray-900 border-b border-gray-100 pb-2">1. Shipping & Billing Address</h3>
                
                {/* Saved address toggles */}
                {user && config && config.saved_addresses.length > 0 && (
                  <div className="space-y-3">
                    <label className="block text-[10px] font-black uppercase tracking-wider text-gray-500">Saved Addresses</label>
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                      {config.saved_addresses.map((addr) => (
                        <label
                          key={addr.id}
                          className={`rounded-2xl border p-4 flex items-start gap-3 cursor-pointer transition-all ${
                            selectedAddressId === addr.id
                              ? "border-blue-600 bg-blue-50/10 shadow-sm"
                              : "border-gray-150 hover:border-gray-300"
                          }`}
                        >
                          <input
                            type="radio"
                            name="saved_address"
                            checked={selectedAddressId === addr.id}
                            onChange={() => handleSavedAddressChange(addr.id)}
                            className="mt-1"
                          />
                          <div className="text-xs space-y-0.5 text-gray-700">
                            <p className="font-bold text-gray-900">{addr.name}</p>
                            <p>{addr.address_line1}</p>
                            {addr.address_line2 && <p>{addr.address_line2}</p>}
                            <p>{addr.city}, {addr.state || ""} {addr.postal_code || ""}</p>
                            <p className="font-semibold text-gray-400 uppercase text-[10px]">{addr.country_code}</p>
                          </div>
                        </label>
                      ))}
                    </div>
                  </div>
                )}

                {/* Direct fields */}
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label className="block text-[9px] font-bold text-gray-500 uppercase tracking-wider mb-1">Email Address</label>
                    <input
                      type="email"
                      required
                      value={email}
                      onChange={(e) => setEmail(e.target.value)}
                      className="w-full h-10 px-3 rounded-xl border border-gray-200 text-xs focus:outline-none focus:border-blue-500"
                    />
                  </div>
                  <div>
                    <label className="block text-[9px] font-bold text-gray-500 uppercase tracking-wider mb-1">Contact Phone</label>
                    <input
                      type="text"
                      required
                      value={phone}
                      onChange={(e) => setPhone(e.target.value)}
                      className="w-full h-10 px-3 rounded-xl border border-gray-200 text-xs focus:outline-none focus:border-blue-500"
                    />
                  </div>
                  <div className="sm:col-span-2">
                    <label className="block text-[9px] font-bold text-gray-500 uppercase tracking-wider mb-1">Full Name</label>
                    <input
                      type="text"
                      required
                      value={billingName}
                      onChange={(e) => setBillingName(e.target.value)}
                      className="w-full h-10 px-3 rounded-xl border border-gray-200 text-xs focus:outline-none focus:border-blue-500"
                    />
                  </div>
                  <div className="sm:col-span-2">
                    <label className="block text-[9px] font-bold text-gray-500 uppercase tracking-wider mb-1">Street Address</label>
                    <input
                      type="text"
                      required
                      value={billingAddress1}
                      onChange={(e) => setBillingAddress1(e.target.value)}
                      placeholder="Street number and name"
                      className="w-full h-10 px-3 rounded-xl border border-gray-200 text-xs focus:outline-none focus:border-blue-500"
                    />
                  </div>
                  <div className="sm:col-span-2">
                    <label className="block text-[9px] font-bold text-gray-500 uppercase tracking-wider mb-1">Apartment, Suite (Optional)</label>
                    <input
                      type="text"
                      value={billingAddress2}
                      onChange={(e) => setBillingAddress2(e.target.value)}
                      placeholder="Apartment, suite, unit etc."
                      className="w-full h-10 px-3 rounded-xl border border-gray-200 text-xs focus:outline-none focus:border-blue-500"
                    />
                  </div>
                  <div>
                    <label className="block text-[9px] font-bold text-gray-500 uppercase tracking-wider mb-1">City</label>
                    <input
                      type="text"
                      required
                      value={billingCity}
                      onChange={(e) => setBillingCity(e.target.value)}
                      className="w-full h-10 px-3 rounded-xl border border-gray-200 text-xs focus:outline-none focus:border-blue-500"
                    />
                  </div>
                  <div>
                    <label className="block text-[9px] font-bold text-gray-500 uppercase tracking-wider mb-1">State / Province</label>
                    <input
                      type="text"
                      value={billingState}
                      onChange={(e) => setBillingState(e.target.value)}
                      className="w-full h-10 px-3 rounded-xl border border-gray-200 text-xs focus:outline-none focus:border-blue-500"
                    />
                  </div>
                  <div>
                    <label className="block text-[9px] font-bold text-gray-500 uppercase tracking-wider mb-1">Postal Code</label>
                    <input
                      type="text"
                      value={billingPostalCode}
                      onChange={(e) => setBillingPostalCode(e.target.value)}
                      className="w-full h-10 px-3 rounded-xl border border-gray-200 text-xs focus:outline-none focus:border-blue-500"
                    />
                  </div>
                  <div>
                    <label className="block text-[9px] font-bold text-gray-500 uppercase tracking-wider mb-1">Country</label>
                    <select
                      value={billingCountry}
                      onChange={(e) => setBillingCountry(e.target.value)}
                      className="w-full h-10 px-3 rounded-xl border border-gray-200 bg-white text-xs text-gray-800 focus:outline-none focus:border-blue-500"
                    >
                      {config?.supported_countries.map((c) => (
                        <option key={c.code} value={c.code}>{c.name}</option>
                      ))}
                    </select>
                  </div>
                </div>
              </div>
            )}

            {/* Shipping methods */}
            {(user || checkoutAsGuest) && config && (
              <div className="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm space-y-4">
                <h3 className="text-xs font-black uppercase tracking-wider text-gray-900 border-b border-gray-100 pb-2">2. Shipping Methods</h3>
                <div className="space-y-2">
                  {config.shipping_methods.map((method) => (
                    <label
                      key={method.id}
                      className={`rounded-2xl border p-4 flex items-center justify-between cursor-pointer transition-all ${
                        selectedShippingMethodId === method.id
                          ? "border-blue-600 bg-blue-50/10 shadow-sm"
                          : "border-gray-150 hover:border-gray-300"
                      }`}
                    >
                      <div className="flex items-center gap-3">
                        <input
                          type="radio"
                          name="shipping_method"
                          checked={selectedShippingMethodId === method.id}
                          onChange={() => setSelectedShippingMethodId(method.id)}
                        />
                        <div className="text-xs space-y-0.5">
                          <p className="font-extrabold text-gray-900">{method.name}</p>
                          <p className="text-gray-500 text-[10px]">{method.description}</p>
                        </div>
                      </div>
                      <span className="font-extrabold text-blue-600 text-xs">
                        {currencySymbol}
                        {method.cost.toFixed(2)}
                      </span>
                    </label>
                  ))}
                </div>
              </div>
            )}

            {/* Payment methods */}
            {(user || checkoutAsGuest) && config && (
              <div className="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm space-y-5">
                <h3 className="text-xs font-black uppercase tracking-wider text-gray-900 border-b border-gray-100 pb-2">3. Payment Methods</h3>
                
                <div className="flex gap-2 p-1 bg-gray-50 rounded-xl border border-gray-150 max-w-sm">
                  {config.cod_enabled && (
                    <button
                      type="button"
                      onClick={() => setPaymentMethod("cod")}
                      className={`flex-1 h-9 rounded-lg text-xs font-extrabold transition-all ${
                        paymentMethod === "cod"
                          ? "bg-white text-blue-600 shadow-sm"
                          : "text-gray-500 hover:text-gray-900"
                      }`}
                    >
                      Cash on Delivery
                    </button>
                  )}
                  {config.offline_payment_methods.length > 0 && (
                    <button
                      type="button"
                      onClick={() => setPaymentMethod("offline")}
                      className={`flex-1 h-9 rounded-lg text-xs font-extrabold transition-all ${
                        paymentMethod === "offline"
                          ? "bg-white text-indigo-600 shadow-sm"
                          : "text-gray-500 hover:text-gray-900"
                      }`}
                    >
                      Offline Payment
                    </button>
                  )}
                </div>

                {paymentMethod === "offline" && (
                  <div className="space-y-4 pt-2 animate-in fade-in duration-200">
                    <div>
                      <label className="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-1">Select Bank Account</label>
                      <select
                        value={offlineMethodId || ""}
                        onChange={(e) => setOfflineMethodId(Number(e.target.value))}
                        className="w-full h-10 px-3 rounded-xl border border-gray-200 bg-white text-xs text-gray-800 focus:outline-none focus:border-blue-500"
                      >
                        {config.offline_payment_methods.map((method) => (
                          <option key={method.id} value={method.id}>{method.name}</option>
                        ))}
                      </select>
                    </div>

                    {/* Display active account instructions */}
                    {offlineMethodId && (
                      <div className="rounded-2xl bg-indigo-50/50 border border-indigo-100/50 p-4 space-y-1.5">
                        <p className="text-[10px] font-black uppercase tracking-wider text-indigo-600">Payment Instructions</p>
                        <p className="text-xs text-indigo-900 font-semibold whitespace-pre-wrap leading-relaxed">
                          {config.offline_payment_methods.find((m) => m.id === offlineMethodId)?.instructions}
                        </p>
                      </div>
                    )}

                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                      <div>
                        <label className="block text-[9px] font-bold text-gray-500 uppercase tracking-wider mb-1">Transaction ID / Reference</label>
                        <input
                          type="text"
                          required={paymentMethod === "offline"}
                          value={offlineReference}
                          onChange={(e) => setOfflineReference(e.target.value)}
                          placeholder="e.g. TRX987239"
                          className="w-full h-10 px-3 rounded-xl border border-gray-200 text-xs focus:outline-none"
                        />
                      </div>
                      <div>
                        <label className="block text-[9px] font-bold text-gray-500 uppercase tracking-wider mb-1">Proof of Payment Receipt</label>
                        <input
                          type="file"
                          accept="image/*"
                          onChange={handleReceiptUpload}
                          className="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                        />
                      </div>
                    </div>
                  </div>
                )}
              </div>
            )}
          </div>

          {/* RIGHT: Totals summary and checkout */}
          <div className="space-y-6">
            
            {/* Totals Summary */}
            <div className="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm space-y-5">
              <h3 className="text-xs font-black uppercase tracking-wider text-gray-900 border-b border-gray-100 pb-2">Order Review</h3>
              
              {/* Dynamic Items Map */}
              <div className="max-h-48 overflow-y-auto divide-y divide-gray-50 pr-1 no-scrollbar">
                {resolvedCart.map((item) => (
                  <div key={`${item.product_id}-${item.variation_id || "base"}`} className="py-2.5 flex items-center justify-between gap-4 text-xs">
                    <div className="flex-grow">
                      <p className="font-bold text-gray-800 line-clamp-1">{item.name}</p>
                      <p className="text-[10px] text-gray-400 font-semibold mt-0.5">Qty: {item.quantity}</p>
                    </div>
                    <span className="font-extrabold text-gray-800">
                      {currencySymbol}
                      {(item.price * item.quantity).toFixed(2)}
                    </span>
                  </div>
                ))}
              </div>

              {/* Coupon input */}
              <div className="border-t border-gray-100 pt-4">
                {appliedCoupon ? (
                  <div className="rounded-2xl bg-emerald-50 border border-emerald-100 p-3 flex items-center justify-between gap-2 text-xs font-bold text-emerald-700 animate-in fade-in duration-200">
                    <span>Code <strong className="text-emerald-900 uppercase">{appliedCoupon.code}</strong> Applied!</span>
                    <button
                      type="button"
                      onClick={handleRemoveCoupon}
                      className="text-[10px] uppercase font-black tracking-wider text-rose-500 hover:text-rose-600"
                    >
                      Remove
                    </button>
                  </div>
                ) : (
                  <form onSubmit={handleApplyCoupon} className="space-y-2">
                    <label className="block text-[9px] font-bold text-gray-400 uppercase tracking-wider">Have a Coupon?</label>
                    
                    {couponError && (
                      <p className="text-[10px] text-rose-600 font-semibold">⚠️ {couponError}</p>
                    )}

                    <div className="flex gap-1.5 relative">
                      {couponLoading && (
                        <div className="absolute inset-0 bg-white/70 flex items-center justify-center z-10 rounded-xl">
                          <div className="h-4 w-4 animate-spin rounded-full border-2 border-blue-600 border-t-transparent"></div>
                        </div>
                      )}
                      <input
                        type="text"
                        placeholder="Coupon Code"
                        value={couponCode}
                        onChange={(e) => setCouponCode(e.target.value)}
                        className="flex-grow h-9 px-3 rounded-xl border border-gray-200 text-xs focus:outline-none uppercase"
                      />
                      <button
                        type="submit"
                        className="h-9 px-4 rounded-xl bg-slate-900 hover:bg-slate-950 text-white font-bold text-[10px] uppercase tracking-wider transition-all active:scale-95"
                      >
                        Apply
                      </button>
                    </div>
                  </form>
                )}
              </div>

              {/* Summary Calculations */}
              <div className="border-t border-gray-100 pt-4 space-y-2 text-xs">
                <div className="flex justify-between items-center text-gray-500 font-semibold">
                  <span>Cart Subtotal</span>
                  <span className="font-bold text-gray-800">
                    {currencySymbol}
                    {cartSubtotal.toFixed(2)}
                  </span>
                </div>
                
                <div className="flex justify-between items-center text-gray-500 font-semibold">
                  <span>Shipping Cost</span>
                  <span className="font-bold text-gray-800">
                    {selectedShippingMethodId ? (
                      <>+{currencySymbol}{shippingCost.toFixed(2)}</>
                    ) : (
                      "Free"
                    )}
                  </span>
                </div>

                {appliedCoupon && (
                  <div className="flex justify-between items-center text-emerald-600 font-bold">
                    <span>Coupon Savings</span>
                    <span>-{currencySymbol}{couponDiscount.toFixed(2)}</span>
                  </div>
                )}

                <div className="flex justify-between items-center text-sm font-black text-gray-950 border-t border-gray-100 pt-3">
                  <span>Final Total</span>
                  <span className="text-lg text-blue-600">
                    {currencySymbol}
                    {orderTotal.toFixed(2)}
                  </span>
                </div>
              </div>

              {/* Submit trigger */}
              <button
                type="submit"
                disabled={!(user || checkoutAsGuest)}
                className="w-full h-11 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs uppercase tracking-wider flex items-center justify-center transition-all active:scale-95 disabled:opacity-50 disabled:pointer-events-none shadow-md shadow-blue-500/10 hover:shadow-blue-500/20"
              >
                Place Order Securely
              </button>
            </div>

          </div>

         </form>
      </main>

      <Footer />
    </div>
  );
}
