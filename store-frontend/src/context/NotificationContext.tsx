"use client";

import React, { createContext, useContext, useState, useEffect } from "react";

export type NotificationType = "success" | "error" | "info" | "warning";

export interface Notification {
  id: string;
  message: string;
  type: NotificationType;
  duration: number;
}

interface NotificationContextType {
  showNotification: (
    message: string,
    type?: NotificationType,
    duration?: number
  ) => void;
}

const NotificationContext = createContext<NotificationContextType | undefined>(
  undefined
);

function ToastItem({
  notification,
  onClose,
}: {
  notification: Notification;
  onClose: () => void;
}) {
  useEffect(() => {
    const timer = setTimeout(() => {
      onClose();
    }, notification.duration);

    return () => clearTimeout(timer);
  }, [notification, onClose]);

  // Premium SVG Icons
  const icons = {
    success: (
      <svg
        className="w-5 h-5 text-emerald-600 flex-shrink-0"
        fill="none"
        stroke="currentColor"
        strokeWidth="2.5"
        viewBox="0 0 24 24"
      >
        <path
          strokeLinecap="round"
          strokeLinejoin="round"
          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
        />
      </svg>
    ),
    error: (
      <svg
        className="w-5 h-5 text-rose-600 flex-shrink-0"
        fill="none"
        stroke="currentColor"
        strokeWidth="2.5"
        viewBox="0 0 24 24"
      >
        <path
          strokeLinecap="round"
          strokeLinejoin="round"
          d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
        />
      </svg>
    ),
    warning: (
      <svg
        className="w-5 h-5 text-amber-600 flex-shrink-0"
        fill="none"
        stroke="currentColor"
        strokeWidth="2.5"
        viewBox="0 0 24 24"
      >
        <path
          strokeLinecap="round"
          strokeLinejoin="round"
          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
        />
      </svg>
    ),
    info: (
      <svg
        className="w-5 h-5 text-blue-600 flex-shrink-0"
        fill="none"
        stroke="currentColor"
        strokeWidth="2.5"
        viewBox="0 0 24 24"
      >
        <path
          strokeLinecap="round"
          strokeLinejoin="round"
          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
        />
      </svg>
    ),
  };

  const borders = {
    success: "border-emerald-500/20 bg-white/95 text-emerald-950 shadow-emerald-500/5",
    error: "border-rose-500/20 bg-white/95 text-rose-950 shadow-rose-500/5",
    warning: "border-amber-500/20 bg-white/95 text-amber-950 shadow-amber-500/5",
    info: "border-blue-500/20 bg-white/95 text-blue-950 shadow-blue-500/5",
  };

  const barColors = {
    success: "bg-emerald-500",
    error: "bg-rose-500",
    warning: "bg-amber-500",
    info: "bg-blue-500",
  };

  return (
    <div
      className={`relative w-80 max-w-full rounded-2xl border p-4 shadow-2xl backdrop-blur-md flex items-center justify-between gap-3 pointer-events-auto overflow-hidden animate-in slide-in-from-right-10 fade-in duration-300 ${borders[notification.type]}`}
    >
      <div className="flex items-center gap-3 flex-1 min-w-0">
        {icons[notification.type]}
        <span className="text-[11px] font-extrabold tracking-wide leading-relaxed break-words pr-2 flex-1 text-gray-800">
          {notification.message}
        </span>
      </div>

      <button
        onClick={onClose}
        className="text-gray-400 hover:text-gray-900 transition-colors p-0.5 rounded-full hover:bg-black/5 flex items-center justify-center flex-shrink-0"
        aria-label="Dismiss Alert"
      >
        <svg className="w-4 h-4" fill="none" stroke="currentColor" strokeWidth="2.5" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>

      {/* Progress bar timer (buttery smooth CSS hardware-accelerated keyframe animation) */}
      <div className="absolute bottom-0 left-0 right-0 h-1 bg-black/5">
        <style>{`
          @keyframes shrink-progress-${notification.id} {
            from { width: 100%; }
            to { width: 0%; }
          }
        `}</style>
        <div
          className={`h-full ${barColors[notification.type]}`}
          style={{
            animation: `shrink-progress-${notification.id} ${notification.duration}ms linear forwards`
          }}
        />
      </div>
    </div>
  );
}

export function NotificationProvider({ children }: { children: React.ReactNode }) {
  const [notifications, setNotifications] = useState<Notification[]>([]);

  const showNotification = (
    message: string,
    type: NotificationType = "success",
    duration: number = 4000
  ) => {
    const id = Math.random().toString(36).substring(2, 9);
    setNotifications((prev) => [...prev, { id, message, type, duration }]);
  };

  const removeNotification = (id: string) => {
    setNotifications((prev) => prev.filter((item) => item.id !== id));
  };

  return (
    <NotificationContext.Provider value={{ showNotification }}>
      {children}
      <div className="fixed top-20 right-4 z-[99999] flex flex-col gap-2.5 pointer-events-none">
        {notifications.map((n) => (
          <ToastItem
            key={n.id}
            notification={n}
            onClose={() => removeNotification(n.id)}
          />
        ))}
      </div>
    </NotificationContext.Provider>
  );
}

export function useNotification() {
  const context = useContext(NotificationContext);
  if (!context) {
    throw new Error(
      "useNotification must be used within a NotificationProvider"
    );
  }
  return context;
}
