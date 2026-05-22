"use client";
import React, { useEffect, useState } from 'react';
import { useNotification } from '@/context/NotificationContext';

interface OrderItem {
  name: string;
  price: number;
  quantity: number;
  attributes?: any;
}

interface Order {
  id: number;
  order_number: string;
  status: string;
  payment_status: string;
  subtotal: number;
  discount: number;
  shipping_cost: number;
  total: number;
  created_at_humans: string;
  items: OrderItem[];
}

export default function Orders() {
  const { showNotification } = useNotification();
  const [orders, setOrders] = useState<Order[]>([]);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    setLoading(true);
    fetch('/api/auth/user')
      .then((res) => res.json())
      .then((data) => {
        setOrders(data.orders || []);
      })
      .catch(() => showNotification('Failed to load orders.', 'error'))
      .finally(() => setLoading(false));
  }, []);

  if (loading) return <div className="loading">Loading orders…</div>;
  if (!orders.length) return <div className="no-data">No recent orders found.</div>;

  return (
    <div className="orders-component">
      <h2 className="section-title">Recent Orders</h2>
      <ul className="order-list">
        {orders.map((order) => (
          <li key={order.id} className="order-item">
            <div className="order-header">
              <strong>Order #{order.order_number}</strong> – {order.status} – {order.created_at_humans}
            </div>
            <div className="order-summary">
              <span>Subtotal: ${order.subtotal.toFixed(2)}</span>
              <span>Discount: ${order.discount.toFixed(2)}</span>
              <span>Shipping: ${order.shipping_cost.toFixed(2)}</span>
              <strong>Total: ${order.total.toFixed(2)}</strong>
            </div>
            <ul className="order-items">
              {order.items.map((item, idx) => (
                <li key={idx} className="order-item-detail">
                  {item.name} × {item.quantity} – ${item.price.toFixed(2)}
                </li>
              ))}
            </ul>
          </li>
        ))}
      </ul>
    </div>
  );
}
