import { cn } from '@/lib/utils';

export function SkeletonCard({ className }: { className?: string }) {
  return (
    <div className={cn('rounded-[18px] overflow-hidden border border-black/8 animate-pulse', className)}>
      <div className="h-44 shimmer" />
      <div className="p-4 space-y-2.5">
        <div className="h-2.5 w-20 bg-paper-3 rounded" />
        <div className="h-4 w-3/4 bg-paper-3 rounded" />
        <div className="h-3 w-full bg-paper-3 rounded" />
        <div className="h-3 w-5/6 bg-paper-3 rounded" />
        <div className="flex gap-1.5 pt-1">
          <div className="h-5 w-12 bg-paper-3 rounded-full" />
          <div className="h-5 w-16 bg-paper-3 rounded-full" />
        </div>
      </div>
    </div>
  );
}

export function SkeletonGrid({ count = 8, cols = 4 }: { count?: number; cols?: number }) {
  const colsCls: Record<number, string> = {
    2: 'grid-cols-2',
    3: 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
    4: 'grid-cols-2 sm:grid-cols-3 lg:grid-cols-4',
    5: 'grid-cols-2 sm:grid-cols-3 lg:grid-cols-5',
  };
  return (
    <div className={`grid ${colsCls[cols] ?? colsCls[4]} gap-4`}>
      {Array.from({ length: count }).map((_, i) => (
        <SkeletonCard key={i} />
      ))}
    </div>
  );
}
