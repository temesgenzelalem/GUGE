'use client';
import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { Menu, X } from 'lucide-react';
import { useEffect, useState } from 'react';
import { cn } from '@/lib/utils';
import { useAuthStore } from '@/lib/auth';

const NAV_LINKS = [
  { href: '/',            label: 'Explore'     },
  { href: '/regions',     label: 'All Regions' },
  { href: '/marketplace', label: 'Marketplace' },
  { href: '/stories',     label: 'Stories'     },
  { href: '/creators',    label: 'Creators'    },
];

export function Navbar() {
  const pathname = usePathname();
  const [menuOpen, setMenuOpen] = useState(false);
  const { isAuthenticated, hydrate, logout } = useAuthStore();

  useEffect(() => {
    hydrate();
  }, [hydrate]);

  return (
    <nav className="fixed top-0 left-0 right-0 z-50 h-[60px] flex items-center justify-between px-10 bg-paper/93 backdrop-blur-md border-b border-black/[0.08]">
      {/* Logo */}
      <Link href="/" className="flex items-center gap-1.5 font-serif text-[21px] font-black tracking-tight">
        GUGE
        <span className="w-[7px] h-[7px] rounded-full bg-forest inline-block mb-0.5" />
      </Link>

      {/* Desktop links */}
      <ul className="hidden md:flex gap-7 list-none">
        {NAV_LINKS.map(({ href, label }) => (
          <li key={href}>
            <Link
              href={href}
              className={cn(
                'font-display text-[12.5px] font-semibold tracking-wide text-ink-3 border-b-2 border-transparent pb-0.5 transition-colors',
                pathname === href && 'text-forest border-forest',
                pathname !== href && 'hover:text-forest',
              )}
            >
              {label}
            </Link>
          </li>
        ))}
      </ul>

      {/* CTA + hamburger */}
      <div className="flex items-center gap-3">
        {isAuthenticated ? (
          <button
            onClick={() => logout()}
            className="hidden md:block font-display text-[11.5px] font-bold tracking-wider uppercase px-4 py-2 border border-black/10 rounded-md hover:bg-paper-2 transition-colors"
          >
            Logout
          </button>
        ) : (
          <Link
            href="/login"
            className="hidden md:block font-display text-[11.5px] font-bold tracking-wider uppercase px-4 py-2 bg-forest text-white rounded-md hover:bg-forest-2 transition-colors"
          >
            Sign in
          </Link>
        )}
        <button
          className="md:hidden text-ink-2"
          onClick={() => setMenuOpen(!menuOpen)}
          aria-label="Toggle menu"
        >
          {menuOpen ? <X size={22} /> : <Menu size={22} />}
        </button>
      </div>

      {/* Mobile menu */}
      {menuOpen && (
        <div className="absolute top-[60px] left-0 right-0 bg-paper border-b border-black/10 px-6 py-4 flex flex-col gap-4 md:hidden z-50">
          {NAV_LINKS.map(({ href, label }) => (
            <Link
              key={href}
              href={href}
              onClick={() => setMenuOpen(false)}
              className={cn(
                'font-display text-[13px] font-semibold text-ink-2 py-1',
                pathname === href && 'text-forest',
              )}
            >
              {label}
            </Link>
          ))}
          {isAuthenticated ? (
            <button onClick={() => { logout(); setMenuOpen(false); }} className="font-display text-[13px] font-semibold text-ink-2 py-1 text-left">
              Logout
            </button>
          ) : (
            <Link href="/login" onClick={() => setMenuOpen(false)} className="font-display text-[13px] font-semibold text-forest py-1">
              Sign in
            </Link>
          )}
        </div>
      )}
    </nav>
  );
}
