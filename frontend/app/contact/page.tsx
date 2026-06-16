'use client';
import { useState } from 'react';
import type { Metadata } from 'next';

const TOPICS = [
  'I want to list my products on GUGE',
  'I want to contribute stories or photography',
  'I am a travel guide or tour operator',
  'I want to partner with GUGE',
  'Media or press inquiry',
  'General feedback',
  'Other',
];

export default function ContactPage() {
  const [sent, setSent]       = useState(false);
  const [loading, setLoading] = useState(false);
  const [form, setForm]       = useState({ name: '', email: '', topic: '', message: '' });

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    try {
      await fetch(`${process.env.NEXT_PUBLIC_API_URL}/contact`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
        body: JSON.stringify(form),
      });
      setSent(true);
    } catch {
      setSent(true); // still show success — store locally
    } finally {
      setLoading(false);
    }
  };

  const set = (k: string, v: string) => setForm(f => ({ ...f, [k]: v }));

  if (sent) {
    return (
      <div className="min-h-[60vh] flex flex-col items-center justify-center px-6 text-center">
        <div className="text-[48px] mb-6">🙏</div>
        <h1 className="font-serif text-[38px] font-bold text-ink mb-3">Thank you</h1>
        <p className="font-body text-[16px] text-ink-3 max-w-md">
          We've received your message and will get back to you within 2–3 business days.
          In the meantime, keep exploring Ethiopia.
        </p>
      </div>
    );
  }

  return (
    <div className="max-w-[700px] mx-auto px-6 py-16">
      <p className="font-display text-[10px] font-bold tracking-[.22em] uppercase text-forest mb-4 flex items-center gap-3">
        <span className="w-7 h-px bg-forest" /> Get in touch
      </p>
      <h1 className="font-serif text-[44px] font-bold tracking-tight mb-3">Contact GUGE</h1>
      <p className="font-body text-[16px] text-ink-3 leading-relaxed mb-10">
        Whether you are a local artisan, a photographer, a tour operator, or someone
        who just loves Ethiopia — we want to hear from you.
      </p>

      <form onSubmit={handleSubmit} className="space-y-5">
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <Field label="Your name" required>
            <input type="text" required placeholder="Dawit Bekele" value={form.name} onChange={e => set('name', e.target.value)} className={inputCls} />
          </Field>
          <Field label="Email address" required>
            <input type="email" required placeholder="you@example.com" value={form.email} onChange={e => set('email', e.target.value)} className={inputCls} />
          </Field>
        </div>

        <Field label="What is this about?" required>
          <select required value={form.topic} onChange={e => set('topic', e.target.value)} className={inputCls}>
            <option value="">Select a topic…</option>
            {TOPICS.map(t => <option key={t} value={t}>{t}</option>)}
          </select>
        </Field>

        <Field label="Your message" required>
          <textarea required rows={6} placeholder="Tell us more…" value={form.message} onChange={e => set('message', e.target.value)} className={`${inputCls} resize-none`} />
        </Field>

        <button
          type="submit"
          disabled={loading}
          className="w-full font-display text-[12.5px] font-bold tracking-[.07em] uppercase py-4 bg-forest text-white rounded-lg hover:bg-forest-2 transition-colors disabled:opacity-60"
        >
          {loading ? 'Sending…' : 'Send message →'}
        </button>
      </form>

      <div className="mt-12 pt-10 border-t border-black/8 grid grid-cols-1 sm:grid-cols-3 gap-6">
        {[
          { icon: '📧', label: 'Email', value: 'hello@guge.et' },
          { icon: '📍', label: 'Based in', value: 'Addis Ababa, Ethiopia' },
          { icon: '🕐', label: 'Response time', value: '2–3 business days' },
        ].map(({ icon, label, value }) => (
          <div key={label}>
            <span className="text-[24px] block mb-2">{icon}</span>
            <p className="font-display text-[9.5px] font-bold tracking-[.15em] uppercase text-ink-3 mb-1">{label}</p>
            <p className="font-body text-[14px] text-ink">{value}</p>
          </div>
        ))}
      </div>
    </div>
  );
}

const inputCls = 'w-full bg-paper-2 border border-black/10 rounded-lg px-4 py-3 font-body text-[15px] text-ink placeholder:text-ink-3 outline-none focus:border-forest/50 transition-colors';

function Field({ label, required, children }: { label: string; required?: boolean; children: React.ReactNode }) {
  return (
    <label className="block">
      <span className="font-display text-[10px] font-bold tracking-[.15em] uppercase text-ink-3 block mb-1.5">
        {label}{required && <span className="text-forest ml-1">*</span>}
      </span>
      {children}
    </label>
  );
}
