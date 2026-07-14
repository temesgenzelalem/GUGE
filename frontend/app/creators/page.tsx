import { getCreators, normalizeCollection } from '@/lib/api';
import { WikiImage } from '@/components/ui/WikiImage';
import Link from 'next/link';

export const revalidate = 3600;
export const metadata = { title: 'Creators — GUGE' };

export default async function CreatorsPage() {
  const res = await getCreators({ per_page: 50 });
  const creators = normalizeCollection(res.data);

  const featuredCreators = creators.filter((creator) => {
    const email = creator.contact_email?.toLowerCase() || '';
    return (
      email === 'temesgenzelalem167@gmail.com' ||
      creator.name.toLowerCase().includes('temesgen')
    );
  });
  const creatorsToShow = featuredCreators.length > 0 ? featuredCreators : creators;

  return (
    <div className="px-10 py-14">
      <div className="mb-10">
        <p className="font-display text-[10px] font-bold tracking-[.2em] uppercase text-forest mb-2">
          Founder & storyteller
        </p>
        <h1 className="font-serif text-[40px] font-bold tracking-tight mb-3">
          Meet the creator behind GUGE
        </h1>
        <p className="font-body text-[16px] text-ink-3 max-w-2xl">
          GUGE brings together travel discovery and cultural marketplace storytelling.
          GUZO is for visiting and exploring places, while DEBEYA is for discovering
          products, makers, and the stories behind what people buy.
        </p>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        {creatorsToShow.map((creator) => (
          <Link
            key={creator.id}
            href={`/creators/${creator.slug}`}
            className="group block rounded-[20px] overflow-hidden border border-black/10 bg-paper transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl"
          >
            <div className="relative h-[200px]">
              <WikiImage
                src={creator.image_url}
                alt={creator.name}
                articleTitle={creator.wiki_article}
                dark
              />
              <div className="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent" />
            </div>
            <div className="p-5">
              <p className="font-display text-[9.5px] font-bold tracking-[.15em] uppercase text-forest mb-1">
                {creator.role}
              </p>
              <h2 className="font-serif text-[20px] font-bold text-ink mb-1">
                {creator.name}
              </h2>
              <p className="font-display text-[9px] font-semibold tracking-[.1em] uppercase text-amber mb-3">
                {creator.region_coverage}
              </p>
              <p className="font-body text-[13px] leading-relaxed text-ink-3 line-clamp-3">
                {creator.bio}
              </p>
            </div>
          </Link>
        ))}
      </div>
    </div>
  );
}
