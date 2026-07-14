import { getRegions, normalizeCollection } from '@/lib/api';
import { RegionCard } from '@/components/sections/RegionCard';
import type { Region } from '@/types';
import { RegionFilters } from '@/components/sections/RegionFilters';

export const revalidate = 3600;
export const metadata = { title: 'All Regions — GUGE' };

interface Props {
  searchParams: { direction?: string; search?: string };
}

export default async function RegionsPage({ searchParams }: Props) {
  const res = await getRegions({
    direction: searchParams.direction,
    search:    searchParams.search,
    per_page:  50,
  });

  const regions = normalizeCollection<Region>(res.data);

  return (
    <div className="px-10 py-14">
      {/* Header */}
      <div className="mb-10">
        <p className="font-display text-[10px] font-bold tracking-[.2em] uppercase text-forest mb-2">
          Complete coverage
        </p>
        <h1 className="font-serif text-[40px] font-bold tracking-tight mb-4">
          All Ethiopian Regions
        </h1>
        <p className="font-body text-[16px] text-ink-3 max-w-xl">
          {res.total} regions, each a complete cultural ecosystem — places,
          products, stories and creators, all connected to where they come from.
        </p>
      </div>

      {/* Filters (client component) */}
      <RegionFilters current={searchParams.direction} />

      {/* Grid */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        {regions.map((region) => (
          <RegionCard key={region.id} region={region} variant="full" />
        ))}
      </div>

      {regions.length === 0 && (
        <p className="text-center font-body text-ink-3 py-20">
          No regions found. Try a different filter.
        </p>
      )}
    </div>
  );
}
