import { getRegion, getRegionProducts, getRegionStories } from '@/lib/api';
import { WikiImage } from '@/components/ui/WikiImage';
import { ProductCard } from '@/components/sections/ProductCard';
import { StoryCard } from '@/components/sections/StoryCard';
import { RegionTabs } from '@/components/sections/RegionTabs';
import { notFound } from 'next/navigation';

export const revalidate = 3600;

interface Props { params: { slug: string } }

export async function generateMetadata({ params }: Props) {
  const res = await getRegion(params.slug).catch(() => null);
  if (!res) return { title: 'Region not found — GUGE' };
  return {
    title: `${res.data.name} — GUGE`,
    description: res.data.description,
  };
}

export default async function RegionPage({ params }: Props) {
  const [regionRes, productsRes, storiesRes] = await Promise.all([
    getRegion(params.slug).catch(() => null),
    getRegionProducts(params.slug).catch(() => ({ data: [] })),
    getRegionStories(params.slug).catch(() => ({ data: [] })),
  ]);

  if (!regionRes) notFound();

  const region   = regionRes.data;
  const products = productsRes.data;
  const stories  = storiesRes.data;

  return (
    <>
      {/* ── HERO ── */}
      <div className="relative bg-ink overflow-hidden">
        {/* bg image */}
        <div className="absolute inset-0 opacity-20">
          <WikiImage src={region.image_url} alt={region.name} dark priority />
        </div>
        <div className="absolute inset-0 bg-gradient-to-r from-ink/95 via-ink/70 to-ink/40" />

        <div className="relative z-10 px-10 pt-14 pb-0">
          {/* breadcrumb */}
          <div className="flex items-center gap-2 font-display text-[11px] font-medium tracking-[.1em] text-white/35 mb-7">
            <a href="/regions" className="hover:text-white/60 transition-colors">Ethiopia</a>
            <span>›</span>
            <span className="text-white/70">{region.zone}</span>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-14 items-start">
            {/* name + tagline */}
            <div>
              <h1 className="font-serif text-[clamp(60px,8vw,100px)] font-black leading-[.9] tracking-[-4px] text-white mb-4">
                {region.name.split('').map((char, i) =>
                  i === Math.floor(region.name.length / 2) ? (
                    <em key={i} className="font-serif italic font-normal text-gold">{char}</em>
                  ) : char
                )}
              </h1>
              <p className="font-body text-[18px] italic leading-[1.6] text-white/55 max-w-lg mb-7">
                {region.tagline}
              </p>
              <div className="flex flex-wrap gap-2 mb-10">
                {region.tags.map((tag) => (
                  <span key={tag} className="font-display text-[10px] font-bold tracking-[.12em] uppercase px-3.5 py-1.5 rounded-full border border-white/20 text-white/60">
                    {tag}
                  </span>
                ))}
              </div>
            </div>

            {/* stats */}
            {region.stats.length > 0 && (
              <div className="grid grid-cols-2 gap-0.5 rounded-[14px] overflow-hidden bg-white/8">
                {region.stats.slice(0, 4).map((stat) => (
                  <div key={stat.label} className="px-5 py-5 bg-white/3">
                    <p className="font-serif text-[32px] font-bold text-gold mb-1">{stat.value}</p>
                    <p className="font-display text-[9.5px] font-medium tracking-[.1em] uppercase text-white/35 leading-snug">
                      {stat.label}
                    </p>
                  </div>
                ))}
              </div>
            )}
          </div>
        </div>

        {/* sticky tab bar */}
        <RegionTabs
          productCount={products.length}
          storyCount={stories.length}
        />
      </div>

      {/* ── PRODUCTS ── */}
      <section id="products" className="px-10 py-12">
        <h2 className="font-serif text-[28px] font-bold mb-2">Local products</h2>
        <p className="font-body text-[14px] text-ink-3 mb-7">
          Authentic goods made in {region.name}, linked to their origin.
        </p>
        {products.length > 0 ? (
          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            {products.map((p) => <ProductCard key={p.id} product={p} />)}
          </div>
        ) : (
          <p className="font-body text-ink-3 py-12 text-center">
            Products coming soon for this region.
          </p>
        )}
      </section>

      {/* ── STORIES ── */}
      <section id="stories" className="px-10 py-12 bg-paper-2">
        <h2 className="font-serif text-[28px] font-bold mb-2">Stories from {region.name}</h2>
        <p className="font-body text-[14px] text-ink-3 mb-7">
          Cultural guides, origin stories and travel essays.
        </p>
        {stories.length > 0 ? (
          <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
            {stories.map((s, i) => <StoryCard key={s.id} story={s} featured={i === 0} />)}
          </div>
        ) : (
          <p className="font-body text-ink-3 py-12 text-center">
            Stories coming soon for this region.
          </p>
        )}
      </section>
    </>
  );
}
