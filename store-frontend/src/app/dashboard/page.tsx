"use client";
import React, { useState, useEffect } from 'react';
import Profile from '@/app/dashboard/components/Profile';
import Addresses from '@/app/dashboard/components/Addresses';
import Orders from '@/app/dashboard/components/Orders';
import '@/app/dashboard/dashboard.css';

export default function DashboardPage() {
  const tabs = [
    { id: 'profile', label: 'Profile' },
    { id: 'addresses', label: 'Address Book' },
    { id: 'orders', label: 'Orders' },
  ];
  const [activeTab, setActiveTab] = useState(tabs[0].id);

  return (
    <div className="dashboard-container">
      <h1 className="dashboard-title">Customer Dashboard</h1>
      <nav className="dashboard-tabs" role="tablist">
        {tabs.map((tab) => (
          <button
            key={tab.id}
            role="tab"
            aria-selected={activeTab === tab.id}
            className={`dashboard-tab ${activeTab === tab.id ? 'active' : ''}`}
            onClick={() => setActiveTab(tab.id)}
          >
            {tab.label}
          </button>
        ))}
      </nav>
      <section className="dashboard-content" aria-live="polite">
        {activeTab === 'profile' && <Profile />}
        {activeTab === 'addresses' && <Addresses />}
        {activeTab === 'orders' && <Orders />}
      </section>
    </div>
  );
}
