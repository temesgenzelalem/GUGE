'use client';
import { useState, useRef, useEffect } from 'react';
import { Search, X } from 'lucide-react';
import { useRouter } from 'next/navigation';
import { globalSearch } from '@/lib/api';
import type { Region, Product, Story } from '@/types';
import Link from 'next/link';

export function SearchBar() {
  const [query, setQuery]       = useState('');
  const [open, setOpen]         = useState(false);
  const [loading, setLoading]   = useState(false);
  const [results, setResults]   = useState<{
    regions: Region[]; products: Product[]; stories: Story[];
  } | null>(null);
  const timer  = useRef<ReturnType<typeof setTimeout> | null>(null);
  const wrapRef = useRef<HTMLDivElement>(null);
  const router = useRouter();

  useEffect(() => {
    const handleClick = (e: MouseEvent) => {
      if (wrapRef.current && !wrapRef.current.contains(e.target as Node)) {
        setOpen(false);
      }
    };
    document.addEventListener('mousedown', handleClick);
    return () => document.removeEventListener('mousedown', handleClick);
  }, []);

  const handleInput = (val: string) => {
    setQuery(val);
    if (timer.current) clearTimeout(timer.current);
    if (val.length < 2) { setResults(null); setOpen(false); return; }
    timer.current = setTimeout(async () => {
      setLoading(true);
      try {
        const res = await globalSearch(val);
        setResults(res);
        setOpen(true);
      } finally {
        setLoading(false);
      }
    }, 320);
  };

  const total = results
    ? results.regions.length + results.products.length + results.stories.length
    : 0;

  return (
    <div ref={wrapRef} className="relative w-full max-w-2xl">
      <div className="relative">
        <Search className="absolute left-4 top-1/2 -translate-y-1/2 text-white/40" size={16} />
        <input
          className="w-full bg-white/8 border border-white/15 rounded-lg py-3 pl-11 pr-10 font-body text-[15px] text-white placeholder:text-white/35 outline-none focus:border-gold transition-colors"
          placeholder="Search a region, destination or product…"
          value={query}
          onChange={(e) => handleInput(e.target.value)}
          onKeyDown={(e) => {
            if (e.key === 'Enter' && query.length > 1) {
              router.push(`/search?q=${encodeURIComponent(query)}`);
              setOpen(false);
            }
          }}
        />
        {query && (
          <button
            onClick={() => { setQuery(''); setResults(null); setOpen(false); }}
            className="absolute right-3 top-1/2 -translate-y-1/2 text-white/40 hover:text-white transition-colors"
          >
            <X size={15} />
          </button>
        )}
      </div>

      {/* Dropdown */}
      {open && results && (
        <div className="absolute top-full left-0 right-0 mt-1.5 bg-paper border border-black/10 rounded-xl shadow-2xl z-50 max-h-[420px] overflow-y-auto">
          {total === 0 ? (
            <p className="px-5 py-4 font-body text-[14px] text-ink-3">
              No results for "{query}"
            </p>
          ) : (
            <>
              {results.regions.length > 0 && (
                <Section label="Regions">
                  {results.regions.map((r) => (
                    <DropdownItem key={r.id} href={`/regions/${r.slug}`} title={r.name} sub={r.zone} onClick={() => setOpen(false)} />
                  ))}
                </Section>
              )}
              {results.products.length > 0 && (
                <Section label="Products">
                  {results.products.map((p) => (
                    <DropdownItem key={p.id} href={`/marketplace/${p.slug}`} title={p.name} sub={p.region?.name} onClick={() => setOpen(false)} />
                  ))}
                </Section>
              )}
              {results.stories.length > 0 && (
                <Section label="Stories">
                  {results.stories.map((s) => (
                    <DropdownItem key={s.id} href={`/stories/${s.slug}`} title={s.title} sub={`${s.read_minutes} min read`} onClick={() => setOpen(false)} />
                  ))}
                </Section>
              )}
            </>
          )}
        </div>
      )}
    </div>
  );
}

function Section({ label, children }: { label: string; children: React.ReactNode }) {
  return (
    <div className="border-b border-black/6 last:border-0">
      <p className="px-4 pt-3 pb-1 font-display text-[9.5px] font-bold tracking-[.18em] uppercase text-ink-3">
        {label}
      </p>
      {children}
    </div>
  );
}

function DropdownItem({ href, title, sub, onClick }: {
  href: string; title: string; sub?: string; onClick: () => void;
}) {
  return (
    <Link
      href={href}
      onClick={onClick}
      className="flex items-center justify-between px-4 py-2.5 hover:bg-paper-2 transition-colors"
    >
      <span className="font-body text-[13.5px] text-ink">{title}</span>
      {sub && <span className="font-display text-[10px] font-semibold tracking-wide text-ink-3 uppercase">{sub}</span>}
    </Link>
  );
}
