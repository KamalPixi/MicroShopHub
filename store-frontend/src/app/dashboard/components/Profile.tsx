"use client";
import React, { useEffect, useState } from 'react';
import { useNotification } from '@/context/NotificationContext';

interface ProfileData {
  id: number;
  name: string;
  email: string;
  phone?: string;
  gender?: number;
  birthday?: string;
  avatar_url?: string;
}

export default function Profile() {
  const { showNotification } = useNotification();
  const [profile, setProfile] = useState<ProfileData | null>(null);
  const [form, setForm] = useState({ name: '', phone: '' });
  const [loading, setLoading] = useState(false);

  // Load current user profile
  useEffect(() => {
    fetch('/api/auth/user')
      .then((res) => res.json())
      .then((data) => {
        setProfile(data.user);
        setForm({ name: data.user.name, phone: data.user.phone ?? '' });
      })
      .catch(() => showNotification('Failed to load profile.', 'error'));
  }, []);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setForm({ ...form, [e.target.name]: e.target.value });
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    fetch('/api/auth/profile', {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(form),
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.message) {
          showNotification(data.message, 'success');
          setProfile((prev) => (prev ? { ...prev, ...form } : prev));
        } else {
          showNotification('Unexpected response.', 'warning');
        }
      })
      .catch(() => showNotification('Failed to update profile.', 'error'))
      .finally(() => setLoading(false));
  };

  if (!profile) return <div className="loading">Loading profile…</div>;

  return (
    <div className="profile-component">
      <h2 className="section-title">Profile Details</h2>
      <form onSubmit={handleSubmit} className="profile-form">
        <label>
          Name
          <input type="text" name="name" value={form.name} onChange={handleChange} required />
        </label>
        <label>
          Phone
          <input type="text" name="phone" value={form.phone} onChange={handleChange} />
        </label>
        <button type="submit" disabled={loading} className="primary-button">
          {loading ? 'Saving…' : 'Save Changes'}
        </button>
      </form>
    </div>
  );
}
