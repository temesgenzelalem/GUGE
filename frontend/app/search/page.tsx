import { globalSearch } from '@/lib/api';
import { RegionCard } from '@/components/sections/RegionCard';
import { ProductCard } from '@/components/sections/ProductCard';
import { StoryCard } from '@/components/sections/StoryCard';
import Link from 'next/link';

export const dynamic = 'force-dynamic';

interface Props { searchParams: { q?: string } }

export function generateMetadata({ searchParams }: Props) {
  return { title: searchParams.q ? `"${searchParams.q}" — Search` : 'Search' };
}

export default async function SearchPage({ searchParams }: Props) {
  const q = (searchParams.q ?? '').trim();

  if (!q) {
    return (
      <div className="px-10 py-20 text-center">
        <h1 className="font-serif text-[40px] font-bold mb-4">Search GUGE</h1>
        <p className="font-body text-ink-3 text-[16px]">Enter a region, product, or story in the search bar above.</p>
      </div>
    );
  }

  const results = await globalSearch(q).catch(() => ({ regions: [], products: [], stories: [] }));
  const total   = results.regions.length + results.products.length + results.stories.length;

  return (
    <div className="px-10 py-14">
      <div className="mb-10">
        <p className="font-display text-[10px] font-bold tracking-[.2em] uppercase text-forest mb-2">
          Search results
        </p>
        <h1 className="font-serif text-[36px] font-bold tracking-tight mb-2">
          "{q}"
        </h1>
        <p className="font-body text-[15px] text-ink-3">
          {total === 0 ? 'No results found.' : `${total} result${total !== 1 ? 's' : ''} across regions, products, and stories.`}
        </p>
      </div>

      {total === 0 && (
        <div className="text-center py-20">
          <p className="font-body text-[17px] text-ink-3 mb-6">Try searching for: Lalibela, coffee, Harar, honey, weaving…</p>
          <Link href="/regions" className="font-display text-[12px] font-bold tracking-[.06em] uppercase px-6 py-3 bg-forest text-white rounded-md hover:bg-forest-2 transition-colors">
            Browse all regions →
          </Link>
        </div>
      )}

      {results.regions.length > 0 && (
        <section className="mb-12">
          <SectionHead label="Regions" count={results.regions.length} href="/regions" />
          <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
            {results.regions.map(r => <RegionCard key={r.id} region={r} variant="tile" />)}
          </div>
        </section>
      )}

      {results.products.length > 0 && (
        <section className="mb-12">
          <SectionHead label="Products" count={results.products.length} href="/marketplace" />
          <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            {results.products.map(p => <ProductCard key={p.id} product={p} />)}
          </div>
        </section>
      )}

      {results.stories.length > 0 && (
        <section className="mb-12">
          <SectionHead label="Stories" count={results.stories.length} href="/stories" />
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            {results.stories.map((s, i) => <StoryCard key={s.id} story={s} featured={i === 0} />)}
          </div>
        </section>
      )}
    </div>
  );
}

function SectionHead({ label, count, href }: { label: string; count: number; href: string }) {
  return (
    <div className="flex items-center justify-between mb-5">
      <h2 className="font-serif text-[24px] font-bold">
        {label} <span className="font-sans text-[16px] font-normal text-ink-3">({count})</span>
      </h2>
      <Link href={href} className="font-display text-[11px] font-bold tracking-[.12em] uppercase text-forest hover:opacity-70 transition-opacity">
        See all →
      </Link>
    </div>
  );
}
