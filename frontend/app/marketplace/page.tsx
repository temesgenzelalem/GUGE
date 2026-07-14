import { getProducts, normalizeCollection } from '@/lib/api';
import { ProductCard } from '@/components/sections/ProductCard';
import { CategoryFilters } from '@/components/sections/CategoryFilters';

export const revalidate = 3600;
export const metadata = { title: 'Marketplace — GUGE' };

const CATEGORIES = ['coffee', 'food', 'craft', 'honey', 'clothing'];

interface Props {
  searchParams: { category?: string; search?: string };
}

export default async function MarketplacePage({ searchParams }: Props) {
  const res = await getProducts({
    category: searchParams.category,
    search:   searchParams.search,
    per_page: 50,
  });

  const products = normalizeCollection(res.data);

  return (
    <div className="px-10 py-14">
      <div className="mb-10">
        <p className="font-display text-[10px] font-bold tracking-[.2em] uppercase text-forest mb-2">
          Authentic Ethiopian goods
        </p>
        <h1 className="font-serif text-[40px] font-bold tracking-tight mb-3">
          Local Marketplace
        </h1>
        <p className="font-body text-[16px] text-ink-3 max-w-xl">
          Every product is linked to its origin region, the people who made it,
          and the culture it comes from. {res.total} products.
        </p>
      </div>

      <CategoryFilters current={searchParams.category} />

      <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
          {products.map((p) => (
            <ProductCard key={p.id} product={p} />
          ))}
      </div>

      {products.length === 0 && (
        <p className="font-body text-ink-3 py-20 text-center">
          No products found for this category.
        </p>
      )}
    </div>
  );
}
