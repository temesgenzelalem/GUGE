import { getStory } from '@/lib/api';
import { WikiImage } from '@/components/ui/WikiImage';
import { StoryCard } from '@/components/sections/StoryCard';
import { notFound } from 'next/navigation';
import Link from 'next/link';

export const revalidate = 3600;

interface Props { params: { slug: string } }

export async function generateMetadata({ params }: Props) {
  const res = await getStory(params.slug).catch(() => null);
  if (!res) return { title: 'Story not found — GUGE' };
  return { title: `${res.data.title} — GUGE`, description: res.data.excerpt };
}

export default async function StoryPage({ params }: Props) {
  const res = await getStory(params.slug).catch(() => null);
  if (!res) notFound();

  const { data: story, related } = res;

  return (
    <article className="max-w-[780px] mx-auto px-6 py-14">
      {/* breadcrumb */}
      <div className="flex items-center gap-2 font-display text-[10.5px] font-semibold tracking-[.1em] uppercase text-ink-3 mb-8">
        <Link href="/stories" className="hover:text-forest transition-colors">Stories</Link>
        <span>›</span>
        <Link href={`/regions/${story.region?.slug}`} className="hover:text-forest transition-colors">
          {story.region?.name}
        </Link>
      </div>

      {/* meta */}
      <div className="flex items-center gap-2 font-display text-[10.5px] font-medium tracking-[.1em] uppercase text-forest mb-4">
        <span>{story.type.replace('-', ' ')}</span>
        <span className="w-1 h-1 rounded-full bg-forest" />
        <span>{story.read_minutes} min read</span>
      </div>

      <h1 className="font-serif text-[38px] lg:text-[46px] font-bold leading-[1.05] tracking-tight text-ink mb-5">
        {story.title}
      </h1>

      <p className="font-body text-[18px] italic leading-[1.7] text-ink-3 mb-8 border-l-2 border-forest pl-4">
        {story.excerpt}
      </p>

      {/* hero image */}
      <div className="relative h-[400px] rounded-[20px] overflow-hidden mb-10">
        <WikiImage src={story.image_url} alt={story.title} dark priority />
        {story.creator && (
          <div className="absolute bottom-4 right-4 z-10 font-display text-[9.5px] font-bold tracking-[.1em] uppercase bg-black/60 text-white/80 px-3 py-1.5 rounded-full backdrop-blur-sm">
            By {story.creator.name}
          </div>
        )}
      </div>

      {/* body */}
      <div className="font-body text-[17px] leading-[1.85] text-ink-2 space-y-5">
        {story.body.split('\n\n').map((para, i) => (
          <p key={i}>{para}</p>
        ))}
      </div>

      {/* region link */}
      {story.region && (
        <div className="mt-12 p-6 bg-forest-3 rounded-[16px] border border-forest/20 flex items-center justify-between">
          <div>
            <p className="font-display text-[9.5px] font-bold tracking-[.15em] uppercase text-forest mb-1">
              Explore the region
            </p>
            <p className="font-serif text-[22px] font-bold text-ink">{story.region.name}</p>
          </div>
          <Link
            href={`/regions/${story.region.slug}`}
            className="font-display text-[11.5px] font-bold tracking-[.08em] uppercase px-5 py-2.5 bg-forest text-white rounded-md hover:bg-forest-2 transition-colors"
          >
            Discover →
          </Link>
        </div>
      )}

      {/* related */}
      {related && related.length > 0 && (
        <div className="mt-14">
          <h2 className="font-serif text-[24px] font-bold mb-6">More from {story.region?.name}</h2>
          <div className="grid grid-cols-1 sm:grid-cols-3 gap-5">
            {related.map((s) => <StoryCard key={s.id} story={s} />)}
          </div>
        </div>
      )}
    </article>
  );
}
