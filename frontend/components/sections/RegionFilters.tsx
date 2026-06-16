'use client';
import { useRouter, useSearchParams } from 'next/navigation';
import { cn } from '@/lib/utils';

const FILTERS = [
  { label: 'All',   value: ''      },
  { label: 'North', value: 'north' },
  { label: 'South', value: 'south' },
  { label: 'East',  value: 'east'  },
  { label: 'West',  value: 'west'  },
];

export function RegionFilters({ current }: { current?: string }) {
  const router = useRouter();
  const params = useSearchParams();

  const setFilter = (value: string) => {
    const p = new URLSearchParams(params.toString());
    if (value) p.set('direction', value);
    else p.delete('direction');
    router.push(`/regions?${p.toString()}`);
  };

  return (
    <div className="flex gap-2 flex-wrap mb-8">
      {FILTERS.map(({ label, value }) => (
        <button
          key={value}
          onClick={() => setFilter(value)}
          className={cn(
            'font-display text-[10.5px] font-bold tracking-[.1em] uppercase px-4 py-2 rounded-full border transition-all',
            (current ?? '') === value
              ? 'bg-forest border-forest text-white'
              : 'border-black/12 text-ink-3 hover:border-forest hover:text-forest',
          )}
        >
          {label}
        </button>
      ))}
    </div>
  );
}
