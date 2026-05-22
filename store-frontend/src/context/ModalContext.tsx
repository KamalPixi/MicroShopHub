"use client";

import React, { createContext, useContext, useState, useEffect, useRef } from "react";

interface ModalOptions {
  closeOnBackdropClick?: boolean;
  size?: "sm" | "md" | "lg" | "xl";
}

interface ModalContextType {
  isOpen: boolean;
  openModal: (content: React.ReactNode, options?: ModalOptions) => void;
  closeModal: () => void;
}

const ModalContext = createContext<ModalContextType | undefined>(undefined);

export function ModalProvider({ children }: { children: React.ReactNode }) {
  const [isOpen, setIsOpen] = useState(false);
  const [content, setContent] = useState<React.ReactNode>(null);
  const [options, setOptions] = useState<ModalOptions>({});

  const openModal = (modalContent: React.ReactNode, modalOptions: ModalOptions = {}) => {
    setContent(modalContent);
    setOptions(modalOptions);
    setIsOpen(true);
  };

  const closeModal = () => {
    setIsOpen(false);
    // Delay clearing content to allow closing animations to finish
    setTimeout(() => {
      setContent(null);
      setOptions({});
    }, 200);
  };

  return (
    <ModalContext.Provider value={{ isOpen, openModal, closeModal }}>
      {children}
      <ModalShell
        isOpen={isOpen}
        onClose={closeModal}
        closeOnBackdropClick={options.closeOnBackdropClick ?? true}
        size={options.size ?? "md"}
      >
        {content}
      </ModalShell>
    </ModalContext.Provider>
  );
}

export function useModal() {
  const context = useContext(ModalContext);
  if (!context) {
    throw new Error("useModal must be used within a ModalProvider");
  }
  return context;
}

interface ModalShellProps {
  isOpen: boolean;
  onClose: () => void;
  children: React.ReactNode;
  closeOnBackdropClick: boolean;
  size: "sm" | "md" | "lg" | "xl";
}

function ModalShell({
  isOpen,
  onClose,
  children,
  closeOnBackdropClick,
  size,
}: ModalShellProps) {
  const modalRef = useRef<HTMLDivElement>(null);

  // Close on Escape key press
  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.key === "Escape") {
        onClose();
      }
    };

    if (isOpen) {
      window.addEventListener("keydown", handleKeyDown);
      document.body.style.overflow = "hidden"; // Lock background scroll
    } else {
      document.body.style.overflow = "";
    }

    return () => {
      window.removeEventListener("keydown", handleKeyDown);
      document.body.style.overflow = "";
    };
  }, [isOpen, onClose]);

  if (!isOpen) return null;

  const sizeClasses = {
    sm: "max-w-sm",
    md: "max-w-md",
    lg: "max-w-lg",
    xl: "max-w-2xl",
  };

  return (
    <div
      className="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm transition-all duration-300 animate-in fade-in duration-200"
      onClick={() => {
        if (closeOnBackdropClick) onClose();
      }}
    >
      <div
        ref={modalRef}
        className={`relative w-full ${sizeClasses[size]} max-h-[90vh] md:max-h-[85vh] flex flex-col overflow-hidden rounded-3xl bg-white p-6 shadow-2xl border border-gray-100 animate-in zoom-in-95 duration-200 text-left`}
        onClick={(e) => e.stopPropagation()} // Prevent clicking inside the modal from closing it
      >
        {/* Universal Close Button */}
        <button
          onClick={onClose}
          className="absolute right-4 top-4 rounded-full p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-700 transition-all active:scale-90 z-50"
          aria-label="Close modal"
        >
          <svg className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="2.5" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" d="M6 18 18 6M6 6l12 12" />
          </svg>
        </button>

        {/* Modal Content Frame */}
        <div className="w-full h-full flex flex-col">
          {children}
        </div>
      </div>
    </div>
  );
}
