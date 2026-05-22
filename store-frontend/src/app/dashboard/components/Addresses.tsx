"use client";
import React, { useEffect, useState } from 'react';
import { useNotification } from '@/context/NotificationContext';

interface Address {
  id: number;
  type: string;
  name: string;
  phone?: string;
  address_line1: string;
  address_line2?: string;
  city: string;
  state?: string;
  postal_code?: string;
  country_code: string;
  is_default?: boolean;
}

export default function Addresses() {
  const { showNotification } = useNotification();
  const [addresses, setAddresses] = useState<Address[]>([]);
  const [newAddress, setNewAddress] = useState({
    type: 'home',
    name: '',
    phone: '',
    address_line1: '',
    address_line2: '',
    city: '',
    state: '',
    postal_code: '',
    country_code: '',
    is_default: false,
  });
  const [loading, setLoading] = useState(false);

  const loadAddresses = () => {
    fetch('/api/auth/user')
      .then((res) => res.json())
      .then((data) => setAddresses(data.addresses || []))
      .catch(() => showNotification('Failed to load addresses.', 'error'));
  };

  useEffect(() => {
    loadAddresses();
  }, []);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    const { name, value, type, checked } = e.target as HTMLInputElement;
    setNewAddress((prev) => ({
      ...prev,
      [name]: type === 'checkbox' ? checked : value,
    }));
  };

  const handleAdd = (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    fetch('/api/auth/address', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(newAddress),
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.message) {
          showNotification(data.message, 'success');
          loadAddresses();
          setNewAddress({
            type: 'home',
            name: '',
            phone: '',
            address_line1: '',
            address_line2: '',
            city: '',
            state: '',
            postal_code: '',
            country_code: '',
            is_default: false,
          });
        } else {
          showNotification('Unexpected response.', 'warning');
        }
      })
      .catch(() => showNotification('Failed to add address.', 'error'))
      .finally(() => setLoading(false));
  };

  const handleDelete = (id: number) => {
    if (!window.confirm('Delete this address?')) return;
    fetch(`/api/auth/address/${id}`, { method: 'DELETE' })
      .then((res) => res.json())
      .then((data) => {
        showNotification(data.message || 'Address removed.', 'success');
        loadAddresses();
      })
      .catch(() => showNotification('Failed to delete address.', 'error'));
  };

  return (
    <div className="addresses-component">
      <h2 className="section-title">Address Book</h2>
      <ul className="address-list">
        {addresses.map((addr) => (
          <li key={addr.id} className="address-item">
            <div>
              <strong>{addr.name}</strong> ({addr.type})
            </div>
            <div>{addr.address_line1}{addr.address_line2 ? `, ${addr.address_line2}` : ''}</div>
            <div>{addr.city}, {addr.state ? `${addr.state}, ` : ''}{addr.country_code}</div>
            <button className="secondary-button" onClick={() => handleDelete(addr.id)}>
              Delete
            </button>
          </li>
        ))}
      </ul>
      <h3 className="subsection-title">Add New Address</h3>
      <form onSubmit={handleAdd} className="address-form">
        <label>
          Type
          <select name="type" value={newAddress.type} onChange={handleChange} required>
            <option value="home">Home</option>
            <option value="office">Office</option>
            <option value="billing">Billing</option>
            <option value="shipping">Shipping</option>
            <option value="other">Other</option>
          </select>
        </label>
        <label>
          Name
          <input type="text" name="name" value={newAddress.name} onChange={handleChange} required />
        </label>
        <label>
          Phone
          <input type="text" name="phone" value={newAddress.phone} onChange={handleChange} />
        </label>
        <label>
          Address Line 1
          <input type="text" name="address_line1" value={newAddress.address_line1} onChange={handleChange} required />
        </label>
        <label>
          Address Line 2
          <input type="text" name="address_line2" value={newAddress.address_line2} onChange={handleChange} />
        </label>
        <label>
          City
          <input type="text" name="city" value={newAddress.city} onChange={handleChange} required />
        </label>
        <label>
          State
          <input type="text" name="state" value={newAddress.state} onChange={handleChange} />
        </label>
        <label>
          Postal Code
          <input type="text" name="postal_code" value={newAddress.postal_code} onChange={handleChange} />
        </label>
        <label>
          Country Code
          <input type="text" name="country_code" value={newAddress.country_code} onChange={handleChange} required />
        </label>
        <label>
          Set as default
          <input type="checkbox" name="is_default" checked={newAddress.is_default} onChange={handleChange} />
        </label>
        <button type="submit" disabled={loading} className="primary-button">
          {loading ? 'Saving…' : 'Add Address'}
        </button>
      </form>
    </div>
  );
}
