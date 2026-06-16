import { getProduct } from '@/lib/api';
import { WikiImage } from '@/components/ui/WikiImage';
import { ProductCard } from '@/components/sections/ProductCard';
import { notFound } from 'next/navigation';
import Link from 'next/link';

export const revalidate = 3600;

interface Props { params: { slug: string } }

export async function generateMetadata({ params }: Props) {
  const res = await getProduct(params.slug).catch(() => null);
  if (!res) return { title: 'Product not found — GUGE' };
  return { title: `${res.data.name} — GUGE`, description: res.data.description };
}

const CONNECTION_NODES = [
  { icon: '🗺️', label: 'Region',    key: 'region'    },
  { icon: '👩‍🌾', label: 'Producer', key: 'producer'  },
  { icon: '📖', label: 'Story',     key: 'story'     },
  { icon: '🌿', label: 'Culture',   key: 'culture'   },
  { icon: '✈️', label: 'Visit',     key: 'visit'     },
  { icon: '🤝', label: 'Community', key: 'community' },
  { icon: '☕', label: 'Ceremony',  key: 'ceremony'  },
  { icon: '📷', label: 'Creator',   key: 'creator'   },
];

export default async function ProductPage({ params }: Props) {
  const res = await getProduct(params.slug).catch(() => null);
  if (!res) notFound();

  const { data: product, related } = res;
  const region = product.region;

  const CATEGORY_EMOJI: Record<string, string> = {
    coffee: '☕', food: '🌾', craft: '🧺', honey: '🍯', clothing: '👘',
  };

  return (
    <div className="max-w-[1200px] mx-auto px-10 py-14">
      {/* ── MAIN GRID ── */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-16 mb-16">

        {/* Image */}
        <div className="relative h-[460px] rounded-[28px] overflow-hidden">
          <WikiImage src={product.image_url} alt={product.name} priority />
          <div className="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent" />
          <span className="absolute top-4 left-4 z-10 font-display text-[9.5px] font-bold tracking-[.15em] uppercase px-3 py-1.5 rounded-full bg-black/40 text-white/80 backdrop-blur-sm">
            {product.category}
          </span>
        </div>

        {/* Details */}
        <div className="flex flex-col justify-center">
          {/* origin path */}
          <div className="flex items-center gap-2 flex-wrap mb-6">
            <span className="font-display text-[10.5px] font-bold tracking-[.1em] uppercase px-3 py-1.5 rounded-full bg-forest-3 text-forest">
              Ethiopia
            </span>
            <span className="text-ink-3 text-sm">›</span>
            {region && (
              <>
                <Link
                  href={`/regions/${region.slug}`}
                  className="font-display text-[10.5px] font-bold tracking-[.1em] uppercase px-3 py-1.5 rounded-full bg-forest-3 text-forest hover:bg-forest hover:text-white transition-colors"
                >
                  {region.zone}
                </Link>
                <span className="text-ink-3 text-sm">›</span>
                <Link
                  href={`/regions/${region.slug}`}
                  className="font-display text-[10.5px] font-bold tracking-[.1em] uppercase px-3 py-1.5 rounded-full bg-forest text-white"
                >
                  {region.name}
                </Link>
              </>
            )}
          </div>

          <h1 className="font-serif text-[44px] font-black leading-[1] tracking-[-1.5px] text-ink mb-3">
            {product.name}
          </h1>
          <p className="font-body text-[16px] italic text-forest mb-5">
            {product.description}
          </p>

          <p className="font-body text-[16px] leading-[1.82] text-ink-2 mb-7">
            {product.story}
          </p>

          {/* tags */}
          <div className="flex flex-wrap gap-2 mb-7">
            {product.tags.map((tag) => (
              <span key={tag} className="font-display text-[9.5px] font-bold tracking-[.1em] uppercase px-2.5 py-1 rounded-full bg-forest-3 text-forest">
                {tag}
              </span>
            ))}
          </div>

          {/* how to order */}
          <div className="p-4 bg-paper-2 rounded-[14px] border border-black/8 mb-6">
            <p className="font-display text-[9.5px] font-bold tracking-[.15em] uppercase text-ink-3 mb-1">
              How to order
            </p>
            <p className="font-body text-[14px] text-ink-2">{product.how_to_order}</p>
          </div>

          <div className="flex gap-3">
            <a
              href="https://wa.me"
              target="_blank"
              rel="noopener noreferrer"
              className="font-display text-[12px] font-bold tracking-[.06em] uppercase px-6 py-3.5 bg-forest text-white rounded-md hover:bg-forest-2 transition-all hover:-translate-y-0.5"
            >
              Contact seller →
            </a>
            {region && (
              <Link
                href={`/regions/${region.slug}`}
                className="font-display text-[12px] font-bold tracking-[.06em] uppercase px-6 py-3.5 text-forest border-b border-forest hover:border-forest-2 transition-colors"
              >
                Visit {region.name} →
              </Link>
            )}
          </div>
        </div>
      </div>

      {/* ── CONNECTIONS GRAPH ── */}
      <div className="mb-16">
        <h2 className="font-serif text-[28px] font-bold mb-2">This product connects to</h2>
        <p className="font-body text-[15px] text-ink-3 mb-8">
          Everything on GUGE links back to the place it came from.
        </p>
        <div className="bg-paper-2 rounded-[28px] p-10 border border-black/8 relative overflow-hidden">
          <div className="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(13,92,67,0.05)_0%,transparent_70%)]" />
          {/* center */}
          <div className="w-20 h-20 rounded-full bg-forest flex items-center justify-center text-3xl mx-auto mb-10 shadow-[0_0_0_16px_rgba(13,92,67,0.1),0_0_0_32px_rgba(13,92,67,0.05)]">
            {CATEGORY_EMOJI[product.category] ?? '📦'}
          </div>
          <div className="grid grid-cols-2 sm:grid-cols-4 gap-3">
            {CONNECTION_NODES.map((node) => (
              <div
                key={node.key}
                className="bg-paper rounded-[14px] p-4 text-center border border-black/8 hover:border-forest hover:bg-forest-3 transition-all cursor-pointer"
              >
                <div className="text-2xl mb-2">{node.icon}</div>
                <p className="font-display text-[9.5px] font-bold tracking-[.1em] uppercase text-ink-3 mb-1">
                  {node.label}
                </p>
                <p className="font-body text-[12.5px] font-medium text-ink">
                  {node.key === 'region' && region?.name}
                  {node.key === 'producer' && 'Local cooperative'}
                  {node.key === 'story' && 'Origin story'}
                  {node.key === 'culture' && region?.zone}
                  {node.key === 'visit'   && 'Plan a trip'}
                  {node.key === 'community' && 'Local sellers'}
                  {node.key === 'ceremony' && 'Traditions'}
                  {node.key === 'creator' && 'GUGE creator'}
                </p>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* ── RELATED ── */}
      {related && related.length > 0 && (
        <div>
          <h2 className="font-serif text-[24px] font-bold mb-6">Related products</h2>
          <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
            {related.map((p) => <ProductCard key={p.id} product={p} />)}
          </div>
        </div>
      )}
    </div>
  );
}
