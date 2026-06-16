'use client';
import { useRouter, useSearchParams } from 'next/navigation';
import { ChevronLeft, ChevronRight } from 'lucide-react';
import { cn } from '@/lib/utils';

interface PaginationProps {
  currentPage: number;
  lastPage: number;
  total: number;
}

export function Pagination({ currentPage, lastPage, total }: PaginationProps) {
  const router = useRouter();
  const params = useSearchParams();

  if (lastPage <= 1) return null;

  const goTo = (page: number) => {
    const p = new URLSearchParams(params.toString());
    p.set('page', String(page));
    router.push(`?${p.toString()}`);
  };

  const pages = Array.from({ length: lastPage }, (_, i) => i + 1).filter(
    p => p === 1 || p === lastPage || Math.abs(p - currentPage) <= 2,
  );

  return (
    <div className="flex items-center justify-center gap-2 mt-12">
      <button
        onClick={() => goTo(currentPage - 1)}
        disabled={currentPage === 1}
        className={cn(pageBtnCls, currentPage === 1 && 'opacity-40 cursor-not-allowed')}
        aria-label="Previous page"
      >
        <ChevronLeft size={16} />
      </button>

      {pages.map((page, i) => (
        <span key={page}>
          {i > 0 && pages[i - 1] !== page - 1 && (
            <span className="px-2 text-ink-3 font-display text-[12px]">…</span>
          )}
          <button
            onClick={() => goTo(page)}
            className={cn(
              pageBtnCls,
              page === currentPage
                ? 'bg-forest text-white border-forest'
                : 'hover:border-forest hover:text-forest',
            )}
          >
            {page}
          </button>
        </span>
      ))}

      <button
        onClick={() => goTo(currentPage + 1)}
        disabled={currentPage === lastPage}
        className={cn(pageBtnCls, currentPage === lastPage && 'opacity-40 cursor-not-allowed')}
        aria-label="Next page"
      >
        <ChevronRight size={16} />
      </button>

      <span className="ml-3 font-display text-[10.5px] font-semibold text-ink-3 tracking-wide">
        {total} total
      </span>
    </div>
  );
}

const pageBtnCls =
  'w-9 h-9 flex items-center justify-center rounded-lg border border-black/10 font-display text-[12px] font-semibold text-ink-2 transition-all hover:shadow-sm';
