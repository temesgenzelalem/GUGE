'use client';
import { cn } from '@/lib/utils';

const TABS = [
  { label: 'Products', href: '#products' },
  { label: 'Stories',  href: '#stories'  },
];

export function RegionTabs({
  productCount,
  storyCount,
}: {
  productCount: number;
  storyCount: number;
}) {
  const counts = [productCount, storyCount];

  return (
    <div className="flex gap-0 border-b border-white/10 mt-0 px-10 bg-ink sticky top-[60px] z-20">
      {TABS.map(({ label, href }, i) => (
        <a
          key={href}
          href={href}
          className={cn(
            'font-display text-[12px] font-bold tracking-[.12em] uppercase py-4 px-6 border-b-2 border-transparent text-white/35 hover:text-white/70 transition-colors',
          )}
        >
          {label}
          {counts[i] > 0 && (
            <span className="ml-1.5 font-display text-[9px] bg-white/10 text-white/50 px-1.5 py-0.5 rounded-full">
              {counts[i]}
            </span>
          )}
        </a>
      ))}
    </div>
  );
}
