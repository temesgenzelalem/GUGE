import { getCreator } from '@/lib/api';
import { WikiImage } from '@/components/ui/WikiImage';
import { StoryCard } from '@/components/sections/StoryCard';
import { notFound } from 'next/navigation';
import Link from 'next/link';

export const revalidate = 3600;
interface Props { params: { slug: string } }

export async function generateMetadata({ params }: Props) {
  const res = await getCreator(params.slug).catch(() => null);
  if (!res) return { title: 'Creator not found — GUGE' };
  return { title: `${res.data.name} — GUGE`, description: res.data.bio };
}

export default async function CreatorPage({ params }: Props) {
  const res = await getCreator(params.slug).catch(() => null);
  if (!res) notFound();
  const { data: creator } = res;
  const isTemesgenProfile =
    creator.contact_email?.toLowerCase() === 'temesgenzelalem167@gmail.com' ||
    creator.name.toLowerCase().includes('temesgen');

  const profileBio = isTemesgenProfile
    ? 'I created GUGE to connect Ethiopia’s places, culture, and products in one experience. GUZO is the travel side of the platform, helping people discover regions, stories, and journeys worth visiting. DEBEYA is the market side, where local goods, craft, and community stories are shared with people who want to support authentic Ethiopian traditions.'
    : creator.bio;

  return (
    <div className="max-w-[900px] mx-auto px-6 py-16">
      {/* Breadcrumb */}
      <div className="flex items-center gap-2 font-display text-[10.5px] font-semibold tracking-[.1em] uppercase text-ink-3 mb-10">
        <Link href="/creators" className="hover:text-forest transition-colors">Creators</Link>
        <span>›</span>
        <span className="text-ink">{creator.name}</span>
      </div>

      {/* Profile */}
      <div className="grid grid-cols-1 md:grid-cols-[260px_1fr] gap-10 mb-14 items-start">
        <div className="relative h-[260px] w-full max-w-[260px] rounded-[24px] overflow-hidden border border-black/10 bg-paper shadow-sm">
          <WikiImage
            src={creator.image_url}
            alt={creator.name}
            articleTitle={creator.wiki_article}
            dark
            priority
          />
        </div>
        <div>
          <p className="font-display text-[9.5px] font-bold tracking-[.18em] uppercase text-forest mb-2">{creator.role}</p>
          <h1 className="font-serif text-[42px] font-bold tracking-tight text-ink mb-2">{creator.name}</h1>
          <p className="font-display text-[10px] font-semibold tracking-[.12em] uppercase text-amber mb-5">{creator.region_coverage}</p>
          <p className="font-body text-[17px] leading-[1.8] text-ink-2 mb-6">{profileBio}</p>
          {creator.contact_email && (
            <a href={`mailto:${creator.contact_email}`} className="font-display text-[11.5px] font-bold tracking-[.08em] uppercase px-5 py-3 bg-forest text-white rounded-md hover:bg-forest-2 transition-colors inline-block">
              Contact {creator.name.split(' ')[0]} →
            </a>
          )}
        </div>
      </div>

      {isTemesgenProfile && (
        <section className="mb-14 rounded-[24px] border border-black/10 bg-[#f7f4ea] p-7">
          <h2 className="font-serif text-[28px] font-bold text-ink mb-3">What GUGE means</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <p className="font-display text-[10px] font-bold tracking-[.18em] uppercase text-forest mb-2">GUZO</p>
              <p className="font-body text-[15px] leading-[1.8] text-ink-2">
                GUZO is the visit side of the platform — a way to explore Ethiopian regions,
                culture, places to go, and the stories that make each destination meaningful.
              </p>
            </div>
            <div>
              <p className="font-display text-[10px] font-bold tracking-[.18em] uppercase text-forest mb-2">DEBEYA</p>
              <p className="font-body text-[15px] leading-[1.8] text-ink-2">
                DEBEYA is the market side — where people can discover local products,
                makers, and authentic Ethiopian goods that connect back to the communities they come from.
              </p>
            </div>
          </div>
        </section>
      )}

      {/* Stories */}
      {creator.stories && creator.stories.length > 0 && (
        <section>
          <h2 className="font-serif text-[28px] font-bold mb-6">Stories by {creator.name}</h2>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            {creator.stories.map((s: any, i: number) => (
              <StoryCard key={s.id} story={s} featured={i === 0} />
            ))}
          </div>
        </section>
      )}
    </div>
  );
}
