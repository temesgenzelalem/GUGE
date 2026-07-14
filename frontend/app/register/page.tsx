'use client';

import Link from 'next/link';
import { useState } from 'react';
import { useRouter } from 'next/navigation';
import { useAuthStore } from '@/lib/auth';
import { useToast } from '@/components/ui/Toast';

export default function RegisterPage() {
  const router = useRouter();
  const register = useAuthStore((state) => state.register);
  const { addToast } = useToast();
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);

  const onSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    try {
      await register(name, email, password);
      addToast('Account created successfully', 'success');
      router.push('/');
    } catch {
      addToast('Unable to create your account right now.', 'error');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="mx-auto flex min-h-[70vh] max-w-[460px] items-center justify-center px-6 py-16">
      <div className="w-full rounded-[24px] border border-black/10 bg-paper p-8 shadow-sm">
        <p className="font-display text-[10px] font-bold tracking-[.2em] uppercase text-forest">Create account</p>
        <h1 className="mt-3 font-serif text-[32px] font-bold text-ink">Join GUGE</h1>
        <p className="mt-2 font-body text-[15px] text-ink-3">Create a profile and start exploring Ethiopia.</p>
        <form className="mt-8 space-y-4" onSubmit={onSubmit}>
          <label className="block">
            <span className="mb-2 block font-display text-[10px] font-bold uppercase tracking-[.15em] text-ink-3">Name</span>
            <input value={name} onChange={(e) => setName(e.target.value)} type="text" required className="w-full rounded-lg border border-black/10 bg-white px-4 py-3 outline-none focus:border-forest" />
          </label>
          <label className="block">
            <span className="mb-2 block font-display text-[10px] font-bold uppercase tracking-[.15em] text-ink-3">Email</span>
            <input value={email} onChange={(e) => setEmail(e.target.value)} type="email" required className="w-full rounded-lg border border-black/10 bg-white px-4 py-3 outline-none focus:border-forest" />
          </label>
          <label className="block">
            <span className="mb-2 block font-display text-[10px] font-bold uppercase tracking-[.15em] text-ink-3">Password</span>
            <input value={password} onChange={(e) => setPassword(e.target.value)} type="password" required className="w-full rounded-lg border border-black/10 bg-white px-4 py-3 outline-none focus:border-forest" />
          </label>
          <button disabled={loading} className="w-full rounded-lg bg-forest px-4 py-3 font-display text-[12px] font-bold uppercase tracking-[.08em] text-white transition hover:bg-forest-2 disabled:opacity-60">
            {loading ? 'Creating account…' : 'Create account'}
          </button>
        </form>
        <p className="mt-6 text-center font-body text-[14px] text-ink-3">
          Already have an account? <Link href="/login" className="font-semibold text-forest">Sign in</Link>
        </p>
      </div>
    </div>
  );
}
