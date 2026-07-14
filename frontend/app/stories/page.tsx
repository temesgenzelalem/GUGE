import { getStories, normalizeCollection } from '@/lib/api';
import { StoryCard } from '@/components/sections/StoryCard';
import type { Story } from '@/types';

export const revalidate = 3600;
export const metadata = { title: 'Stories — GUGE' };

export default async function StoriesPage() {
  const res = await getStories({ per_page: 50 });

  const stories = normalizeCollection<Story>(res.data);
  const [featured, ...rest] = stories as Story[];

  return (
    <div className="px-10 py-14">
      <div className="mb-10">
        <p className="font-display text-[10px] font-bold tracking-[.2em] uppercase text-forest mb-2">
          Cultural storytelling
        </p>
        <h1 className="font-serif text-[40px] font-bold tracking-tight">
          Stories from across Ethiopia
        </h1>
      </div>

      {/* Featured */}
      {featured && (
        <div className="grid grid-cols-1 lg:grid-cols-[1.6fr_1fr] gap-6 mb-10">
          <StoryCard story={featured} featured />
          <div className="flex flex-col gap-6">
            {rest.slice(0, 2).map((s) => (
              <StoryCard key={s.id} story={s} />
            ))}
          </div>
        </div>
      )}

      {/* Rest */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        {rest.slice(2).map((s) => (
          <StoryCard key={s.id} story={s} />
        ))}
      </div>
    </div>
  );
}
