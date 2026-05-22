"use client";

import React, { useState, useEffect } from "react";
import { api, AuthSettings } from "../utils/api";

interface AuthModalProps {
  isOpen: boolean;
  onClose: () => void;
  onSuccess: (user: any) => void;
}

export default function AuthModal({ isOpen, onClose, onSuccess }: AuthModalProps) {
  const [settings, setSettings] = useState<AuthSettings | null>(null);
  const [mode, setMode] = useState<"login" | "register" | "otp">("login");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [name, setName] = useState("");
  const [otp, setOtp] = useState("");
  const [otpSent, setOtpSent] = useState(false);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [message, setMessage] = useState("");

  useEffect(() => {
    if (isOpen) {
      setError("");
      setMessage("");
      // Fetch auth configurations from Laravel
      api.fetchAuthSettings()
        .then((res) => {
          setSettings(res);
          if (res.email_otp_enabled && !res.email_password_enabled) {
            setMode("otp");
          } else {
            setMode("login");
          }
        })
        .catch((err) => {
          console.error("Failed to fetch auth settings", err);
        });
    }
  }, [isOpen]);

  if (!isOpen) return null;

  const handlePasswordLogin = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError("");
    setMessage("");

    try {
      const res = await api.loginPassword(email, password, true);
      setMessage(res.message);
      onSuccess(res.user);
      setTimeout(() => {
        onClose();
      }, 1000);
    } catch (err: any) {
      setError(err.message || "Login failed. Please check credentials.");
    } finally {
      setLoading(false);
    }
  };

  const handleRegister = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError("");
    setMessage("");

    try {
      const res = await api.registerCustomer(name, email, password);
      setMessage(res.message);
      onSuccess(res.user);
      setTimeout(() => {
        onClose();
      }, 1000);
    } catch (err: any) {
      setError(err.message || "Registration failed. Email might be in use.");
    } finally {
      setLoading(false);
    }
  };

  const handleSendOtp = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError("");
    setMessage("");

    try {
      const res = await api.sendOtp(email);
      setOtpSent(true);
      setMessage(res.message || "OTP code has been sent to your email!");
    } catch (err: any) {
      setError(err.message || "Failed to send OTP. Please try again.");
    } finally {
      setLoading(false);
    }
  };

  const handleVerifyOtp = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError("");
    setMessage("");

    try {
      const res = await api.loginOtp(email, otp);
      setMessage(res.message || "Successfully logged in!");
      onSuccess(res.user);
      setTimeout(() => {
        onClose();
      }, 1000);
    } catch (err: any) {
      setError(err.message || "Invalid OTP code. Please try again.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-sm animate-in fade-in duration-200">
      <div className="flex min-h-screen items-center justify-center p-4">
        <div className="relative w-full max-w-md max-h-[90vh] md:max-h-[85vh] flex flex-col overflow-hidden rounded-3xl bg-white p-6 shadow-2xl border border-gray-100 animate-in zoom-in-95 duration-200 text-left">
          
          {/* Close Button */}
          <button
            onClick={onClose}
            className="absolute right-4 top-4 rounded-full p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-700 transition-all active:scale-90"
          >
            <svg className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="2.5" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
          </button>

          {/* Modal Header */}
          <div className="text-center mb-6 flex-shrink-0">
            <h2 className="text-lg font-black text-gray-900 uppercase tracking-tight">
              {mode === "login" ? "Welcome Back" : mode === "register" ? "Create Account" : "Secure Sign In"}
            </h2>
            <p className="text-[11px] text-gray-500 mt-1">
              {mode === "login"
                ? "Access your dynamic dashboard and order tracking"
                : mode === "register"
                ? "Register to save addresses and track orders seamlessly"
                : "Verify your email with a one-time passcode"}
            </p>
          </div>

          {/* Dynamic Alerts */}
          {(error || message) && (
            <div className="flex-shrink-0">
              {error && (
                <div className="mb-4 rounded-2xl bg-rose-50 border border-rose-100 p-3 text-xs text-rose-600 font-medium">
                  ⚠️ {error}
                </div>
              )}
              {message && (
                <div className="mb-4 rounded-2xl bg-emerald-50 border border-emerald-100 p-3 text-xs text-emerald-600 font-medium">
                  ✅ {message}
                </div>
              )}
            </div>
          )}

          {/* Loading Spinner Overlays */}
          {loading && (
            <div className="absolute inset-0 bg-white/70 backdrop-blur-[1px] flex items-center justify-center z-10">
              <div className="h-8 w-8 animate-spin rounded-full border-4 border-blue-600 border-t-transparent"></div>
            </div>
          )}

          {/* Scrollable Form Container */}
          <div className="flex-1 overflow-y-auto pr-1 -mr-1">
            {/* PASSWORD LOGIN FORM */}
            {mode === "login" && (
              <form onSubmit={handlePasswordLogin} className="space-y-4">
                <div>
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1">
                    Email Address
                  </label>
                  <input
                    type="email"
                    required
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    placeholder="you@example.com"
                    className="w-full h-10 px-4 rounded-xl border border-gray-200 text-xs focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 placeholder-gray-400 text-gray-800"
                  />
                </div>
                <div>
                  <div className="flex justify-between items-center mb-1">
                    <label className="block text-[10px] font-bold uppercase tracking-wider text-gray-500">
                      Password
                    </label>
                  </div>
                  <input
                    type="password"
                    required
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    placeholder="••••••••"
                    className="w-full h-10 px-4 rounded-xl border border-gray-200 text-xs focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 placeholder-gray-400 text-gray-800"
                  />
                </div>
                <button
                  type="submit"
                  className="w-full h-10 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider shadow-md shadow-blue-500/10 transition-all active:scale-95"
                >
                  Sign In
                </button>

                <div className="flex flex-col sm:flex-row items-center justify-between pt-3 gap-2 border-t border-gray-100">
                  <button
                    type="button"
                    onClick={() => setMode("register")}
                    className="text-[10px] font-bold text-blue-600 hover:text-blue-700 transition-colors uppercase tracking-wider"
                  >
                    Create an Account
                  </button>
                  {settings?.email_otp_enabled && (
                    <button
                      type="button"
                      onClick={() => {
                        setMode("otp");
                        setOtpSent(false);
                      }}
                      className="text-[10px] font-bold text-indigo-600 hover:text-indigo-700 transition-colors uppercase tracking-wider"
                    >
                      Use OTP Login Instead
                    </button>
                  )}
                </div>
              </form>
            )}

            {/* REGISTER FORM */}
            {mode === "register" && (
              <form onSubmit={handleRegister} className="space-y-4">
                <div>
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1">
                    Full Name
                  </label>
                  <input
                    type="text"
                    required
                    value={name}
                    onChange={(e) => setName(e.target.value)}
                    placeholder="John Doe"
                    className="w-full h-10 px-4 rounded-xl border border-gray-200 text-xs focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 placeholder-gray-400 text-gray-800"
                  />
                </div>
                <div>
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1">
                    Email Address
                  </label>
                  <input
                    type="email"
                    required
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    placeholder="you@example.com"
                    className="w-full h-10 px-4 rounded-xl border border-gray-200 text-xs focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 placeholder-gray-400 text-gray-800"
                  />
                </div>
                <div>
                  <label className="block text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1">
                    Create Password
                  </label>
                  <input
                    type="password"
                    required
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    placeholder="••••••••"
                    className="w-full h-10 px-4 rounded-xl border border-gray-200 text-xs focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 placeholder-gray-400 text-gray-800"
                  />
                </div>
                <button
                  type="submit"
                  className="w-full h-10 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider shadow-md shadow-blue-500/10 transition-all active:scale-95"
                >
                  Sign Up
                </button>

                <div className="flex items-center justify-center pt-3 border-t border-gray-100">
                  <button
                    type="button"
                    onClick={() => setMode("login")}
                    className="text-[10px] font-bold text-blue-600 hover:text-blue-700 transition-colors uppercase tracking-wider"
                  >
                    Already have an account? Sign In
                  </button>
                </div>
              </form>
            )}

            {/* OTP LOGIN FORM */}
            {mode === "otp" && (
              <div className="space-y-4">
                {!otpSent ? (
                  <form onSubmit={handleSendOtp} className="space-y-4">
                    <div>
                      <label className="block text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1">
                        Email Address
                      </label>
                      <input
                        type="email"
                        required
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        placeholder="you@example.com"
                        className="w-full h-10 px-4 rounded-xl border border-gray-200 text-xs focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 placeholder-gray-400 text-gray-800"
                      />
                    </div>
                    <button
                      type="submit"
                      className="w-full h-10 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider shadow-md shadow-indigo-500/10 transition-all active:scale-95"
                    >
                      Send Verification Code
                    </button>
                  </form>
                ) : (
                  <form onSubmit={handleVerifyOtp} className="space-y-4">
                    <div>
                      <p className="text-[11px] text-gray-500 mb-3">
                        We sent a secure validation code to <strong className="text-gray-800">{email}</strong>.
                      </p>
                      <label className="block text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1">
                        Verification Code
                      </label>
                      <input
                        type="text"
                        required
                        maxLength={6}
                        value={otp}
                        onChange={(e) => setOtp(e.target.value)}
                        placeholder="123456"
                        className="w-full h-10 px-4 rounded-xl border border-gray-200 text-xs text-center tracking-[0.4em] font-black focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 placeholder-gray-300 text-gray-800"
                      />
                    </div>
                    <button
                      type="submit"
                      className="w-full h-10 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider shadow-md shadow-emerald-500/10 transition-all active:scale-95"
                    >
                      Confirm & Login
                    </button>
                    <button
                      type="button"
                      onClick={() => setOtpSent(false)}
                      className="w-full text-center text-[10px] font-bold text-gray-500 hover:text-gray-700 transition-colors uppercase tracking-wider"
                    >
                      Change Email or Resend
                    </button>
                  </form>
                )}

                {settings?.email_password_enabled && (
                  <div className="flex items-center justify-center pt-3 border-t border-gray-100">
                    <button
                      type="button"
                      onClick={() => setMode("login")}
                      className="text-[10px] font-bold text-blue-600 hover:text-blue-700 transition-colors uppercase tracking-wider"
                    >
                      Back to Password Login
                    </button>
                  </div>
                )}
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
