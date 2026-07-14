import Link from 'next/link';
import { WikiImage } from '@/components/ui/WikiImage';
import type { Product } from '@/types';

interface ProductCardProps {
  product: Product;
}

const CATEGORY_COLOR: Record<string, string> = {
  coffee:   'text-amber',
  food:     'text-forest',
  craft:    'text-[#8b2310]',
  honey:    'text-gold',
  clothing: 'text-[#533ab7]',
};

export function ProductCard({ product }: ProductCardProps) {
  return (
    <Link
      href={`/marketplace/${product.slug}`}
      className="group block rounded-[18px] overflow-hidden border border-black/10 bg-paper transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
    >
      <div className="relative h-[180px] overflow-hidden">
        <WikiImage src={product.image_url} alt={product.name} articleTitle={product.wiki_article} />
        <div className="absolute bottom-2.5 left-2.5 z-10">
          <span className="font-display text-[9px] font-bold tracking-[0.14em] uppercase px-2 py-1 rounded bg-forest/20 text-forest backdrop-blur-sm">
            {product.region?.name ?? '—'}
          </span>
        </div>
        <div className="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent group-hover:from-black/30 transition-all duration-300" />
      </div>
      <div className="p-4 pb-5">
        <p className={`font-display text-[9px] font-bold tracking-[0.14em] uppercase mb-1 ${CATEGORY_COLOR[product.category] ?? 'text-amber'}`}>
          {product.category}
        </p>
        <h3 className="font-serif text-[17px] font-bold text-ink leading-snug mb-2">{product.name}</h3>
        <p className="text-[12.5px] leading-relaxed text-ink-3 mb-3 line-clamp-2">{product.description}</p>
        <div className="flex gap-1.5 flex-wrap mb-3">
          {(product.tags ?? []).slice(0, 3).map((tag) => (
            <span key={tag} className="font-display text-[9px] font-bold tracking-[0.1em] uppercase px-2 py-0.5 rounded-full bg-forest-3 text-forest">
              {tag}
            </span>
          ))}
        </div>
        <span className="font-display text-[10px] font-bold tracking-[0.1em] uppercase text-forest group-hover:gap-2 transition-all">
          See origin story →
        </span>
      </div>
    </Link>
  );
}
