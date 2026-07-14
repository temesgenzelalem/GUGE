import { getRegions, getProducts, getStories, normalizeCollection } from '@/lib/api';
import type { Region, Product, Story } from '@/types';
import { WikiImage } from '@/components/ui/WikiImage';
import { RegionCard } from '@/components/sections/RegionCard';
import { ProductCard } from '@/components/sections/ProductCard';
import { StoryCard } from '@/components/sections/StoryCard';
import { HeroSection } from '@/components/sections/HeroSection';
import { SearchBar } from '@/components/sections/SearchBar';
import Link from 'next/link';

const FEATURED_HERO_IMAGE = 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/aa/Lalibela%2C_san_giorgio%2C_esterno_24.jpg/1600px-Lalibela%2C_san_giorgio%2C_esterno_24.jpg';

export const revalidate = 3600; // ISR: rebuild every hour

export default async function HomePage() {
  const [regionsRes, productsRes, storiesRes] = await Promise.all([
    getRegions({ per_page: 10 }),
    getProducts({ per_page: 8 }),
    getStories({ per_page: 6 }),
  ]);

  const regions  = normalizeCollection<Region>(regionsRes.data);
  const products = normalizeCollection<Product>(productsRes.data);
  const stories  = normalizeCollection<Story>(storiesRes.data);

  return (
    <>
      {/* ── HERO ── */}
      <HeroSection regions={regions.slice(0, 5)} heroImage={FEATURED_HERO_IMAGE} />

      {/* ── SEARCH ── */}
      <div className="bg-ink px-10 py-7">
        <SearchBar />
      </div>

      {/* ── FEATURED REGIONS ── */}
      <section className="px-10 py-14">
        <div className="flex items-end justify-between mb-8">
          <div>
            <p className="font-display text-[10px] font-bold tracking-[.2em] uppercase text-forest mb-2">
              Regional ecosystems
            </p>
            <h2 className="font-serif text-[34px] font-bold tracking-tight">
              Explore by region
            </h2>
          </div>
          <Link
            href="/regions"
            className="font-display text-[11px] font-bold tracking-[.14em] uppercase text-forest hover:opacity-70 transition-opacity"
          >
            See all {regionsRes.total} regions →
          </Link>
        </div>
        <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
          {regions.slice(0, 10).map((r) => (
            <RegionCard key={r.id} region={r} variant="tile" />
          ))}
        </div>
      </section>

      {/* ── FEATURED PRODUCTS ── */}
      <section className="px-10 py-14 bg-paper-2">
        <div className="flex items-end justify-between mb-8">
          <div>
            <p className="font-display text-[10px] font-bold tracking-[.2em] uppercase text-forest mb-2">
              Local marketplace
            </p>
            <h2 className="font-serif text-[34px] font-bold tracking-tight">
              Products with a place &amp; a story
            </h2>
          </div>
          <Link
            href="/marketplace"
            className="font-display text-[11px] font-bold tracking-[.14em] uppercase text-forest hover:opacity-70 transition-opacity"
          >
            Browse all products →
          </Link>
        </div>
        <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3.5">
          {products.slice(0, 8).map((p) => (
            <ProductCard key={p.id} product={p} />
          ))}
        </div>
      </section>

      {/* ── FEATURED STORIES ── */}
      <section className="px-10 py-14">
        <div className="flex items-end justify-between mb-8">
          <div>
            <p className="font-display text-[10px] font-bold tracking-[.2em] uppercase text-forest mb-2">
              Cultural storytelling
            </p>
            <h2 className="font-serif text-[34px] font-bold tracking-tight">
              Stories from across Ethiopia
            </h2>
          </div>
          <Link
            href="/stories"
            className="font-display text-[11px] font-bold tracking-[.14em] uppercase text-forest hover:opacity-70 transition-opacity"
          >
            Read all stories →
          </Link>
        </div>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
          {stories.slice(0, 3).map((s, i) => (
            <StoryCard key={s.id} story={s} featured={i === 0} />
          ))}
        </div>
      </section>

      {/* ── FOOTER ── */}
      <footer className="bg-ink px-10 py-11 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div>
          <p className="font-serif text-[22px] font-black text-white">GUGE</p>
          <p className="font-body text-[13px] italic text-white/35 mt-0.5">
            "Travel Ethiopia. Buy Ethiopia."
          </p>
        </div>
        <nav className="flex flex-wrap gap-6">
          {[['/', 'Explore'], ['/regions', 'Regions'], ['/marketplace', 'Marketplace'],
            ['/stories', 'Stories'], ['/creators', 'Creators']].map(([href, label]) => (
            <Link
              key={href}
              href={href}
              className="font-display text-[10.5px] font-bold tracking-[.12em] uppercase text-white/38 hover:text-gold transition-colors"
            >
              {label}
            </Link>
          ))}
        </nav>
      </footer>
    </>
  );
}
