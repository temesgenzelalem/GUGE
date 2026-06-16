import Link from 'next/link';
import { WikiImage } from '@/components/ui/WikiImage';
import type { Region } from '@/types';

interface RegionCardProps {
  region: Region;
  variant?: 'tile' | 'full';
}

export function RegionCard({ region, variant = 'tile' }: RegionCardProps) {
  if (variant === 'tile') {
    return (
      <Link
        href={`/regions/${region.slug}`}
        className="group relative block rounded-[18px] overflow-hidden h-[260px] transition-transform duration-300 hover:-translate-y-1.5"
      >
        <div className="absolute inset-0">
          <WikiImage src={region.image_url} alt={region.name} dark priority={false} articleTitle={region.wiki_article} />
        </div>
        {/* gradient */}
        <div className="absolute inset-0 bg-gradient-to-t from-black/78 via-black/20 to-transparent" />
        {/* content */}
        <div className="absolute inset-0 p-4 flex flex-col justify-end z-10">
          <span className="font-display text-[9px] font-bold tracking-[0.18em] uppercase text-white/50 mb-1">
            {region.zone}
          </span>
          <h3 className="font-serif text-[22px] font-bold text-white leading-tight mb-2">
            {region.name}
          </h3>
          <div className="flex gap-1.5 flex-wrap">
            {region.tags.slice(0, 2).map((tag) => (
              <span
                key={tag}
                className="font-display text-[9px] font-bold tracking-[0.1em] uppercase px-2 py-1 rounded-full bg-white/15 text-white/75 backdrop-blur-sm"
              >
                {tag}
              </span>
            ))}
          </div>
        </div>
      </Link>
    );
  }

  // full card for /regions grid
  return (
    <Link
      href={`/regions/${region.slug}`}
      className="group block rounded-[22px] overflow-hidden border border-black/10 bg-paper transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl"
    >
      <div className="relative h-[200px]">
        <WikiImage src={region.image_url} alt={region.name} dark articleTitle={region.wiki_article} />
        <div className="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent" />
      </div>
      <div className="p-5">
        <p className="font-display text-[9px] font-bold tracking-[0.18em] uppercase text-amber mb-1">
          {region.zone}
        </p>
        <h3 className="font-serif text-[22px] font-bold text-ink mb-2">{region.name}</h3>
        <p className="text-[13px] leading-relaxed text-ink-3 mb-3 line-clamp-3">{region.description}</p>
        <div className="flex gap-1.5 flex-wrap">
          {region.tags.map((tag) => (
            <span
              key={tag}
              className="font-display text-[9px] font-bold tracking-[0.1em] uppercase px-2 py-0.5 rounded-full bg-forest-3 text-forest"
            >
              {tag}
            </span>
          ))}
        </div>
      </div>
    </Link>
  );
}
