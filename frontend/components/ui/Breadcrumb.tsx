import Link from 'next/link';
import { ChevronRight } from 'lucide-react';
import { cn } from '@/lib/utils';

export interface Crumb { label: string; href?: string }

export function Breadcrumb({ crumbs, light = false }: { crumbs: Crumb[]; light?: boolean }) {
  return (
    <nav aria-label="Breadcrumb" className="flex items-center flex-wrap gap-1.5">
      {crumbs.map((crumb, i) => (
        <span key={i} className="flex items-center gap-1.5">
          {i > 0 && (
            <ChevronRight
              size={12}
              className={cn('shrink-0', light ? 'text-white/30' : 'text-ink-3')}
            />
          )}
          {crumb.href ? (
            <Link
              href={crumb.href}
              className={cn(
                'font-display text-[10.5px] font-semibold tracking-[.1em] uppercase transition-colors',
                light
                  ? 'text-white/35 hover:text-white/70'
                  : 'text-ink-3 hover:text-forest',
              )}
            >
              {crumb.label}
            </Link>
          ) : (
            <span
              className={cn(
                'font-display text-[10.5px] font-semibold tracking-[.1em] uppercase',
                light ? 'text-white/70' : 'text-ink',
              )}
            >
              {crumb.label}
            </span>
          )}
        </span>
      ))}
    </nav>
  );
}
