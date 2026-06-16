'use client';
import Link from 'next/link';
import { WikiImage } from '@/components/ui/WikiImage';
import type { Region } from '@/types';

interface HeroSectionProps {
  regions: Region[];
}

export function HeroSection({ regions }: HeroSectionProps) {
  return (
    <section className="relative min-h-[calc(100vh-60px)] grid grid-cols-1 lg:grid-cols-2 overflow-hidden bg-paper-2">
      {/* Left — content */}
      <div className="flex flex-col justify-center px-10 lg:px-20 py-16 animate-fade-up z-10">
        <div className="flex items-center gap-3 mb-5">
          <span className="w-7 h-px bg-forest" />
          <span className="font-display text-[10.5px] font-bold tracking-[.22em] uppercase text-forest">
            Ethiopia's living cultural atlas
          </span>
        </div>

        <h1 className="font-serif font-black text-[clamp(44px,6vw,80px)] leading-[.92] tracking-[-3px] mb-6">
          Explore
          <br />
          <em className="font-serif font-normal italic text-forest">Ethiopia</em>
          <br />
          by Region
        </h1>

        <p className="font-body text-[17px] leading-[1.72] text-ink-2 max-w-[400px] mb-9">
          Every region has a story. Every product has an origin. GUGE connects
          Ethiopia's places, cultures, and authentic local goods through deep
          regional discovery.
        </p>

        <div className="flex flex-wrap gap-3">
          <Link
            href="/regions"
            className="font-display text-[12.5px] font-bold tracking-[.06em] uppercase px-6 py-3.5 bg-forest text-white rounded-md hover:bg-forest-2 transition-all hover:-translate-y-0.5 hover:shadow-lg"
          >
            Browse all regions →
          </Link>
          <Link
            href="/marketplace"
            className="font-display text-[12.5px] font-bold tracking-[.06em] uppercase px-6 py-3.5 text-ink-2 border-b border-black/20 hover:text-forest hover:border-forest transition-colors"
          >
            Shop local products
          </Link>
        </div>
      </div>

      {/* Right — mosaic of region images */}
      <div className="hidden lg:grid grid-cols-2 grid-rows-3 gap-1.5 p-5 pl-1.5 animate-fade-up-delay">
        {regions.map((region, i) => (
          <Link
            key={region.id}
            href={`/regions/${region.slug}`}
            className={[
              'relative rounded-[14px] overflow-hidden group',
              i === 0 ? 'row-span-2' : '',
              i === 3 ? 'row-span-2' : '',
            ].join(' ')}
          >
            <WikiImage
              src={region.image_url}
              alt={region.name}
              dark
              priority={i === 0}
              articleTitle={region.wiki_article}
            />
            <div className="absolute inset-0 bg-gradient-to-t from-black/65 via-black/10 to-transparent group-hover:from-black/75 transition-all duration-300" />
            <span className="absolute bottom-3 left-3.5 z-10 font-display text-[9px] font-bold tracking-[.15em] uppercase text-white/80">
              {region.name}
            </span>
          </Link>
        ))}
      </div>
    </section>
  );
}
