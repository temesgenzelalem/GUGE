import Link from 'next/link';
import { WikiImage } from '@/components/ui/WikiImage';
import type { Story } from '@/types';

interface StoryCardProps {
  story: Story;
  featured?: boolean;
}

const TYPE_LABEL: Record<string, string> = {
  'travel':         'Travel',
  'product-origin': 'Product origin',
  'culture':        'Culture',
  'festival':       'Festival',
  'history':        'History',
  'craft':          'Craft',
};

export function StoryCard({ story, featured = false }: StoryCardProps) {
  return (
    <Link
      href={`/stories/${story.slug}`}
      className="group block transition-transform duration-200 hover:-translate-y-1"
    >
      <div className={`relative rounded-[18px] overflow-hidden mb-4 ${featured ? 'h-[340px]' : 'h-[210px]'}`}>
        <WikiImage src={story.image_url} alt={story.title} dark articleTitle={story.wiki_article} />
        <div className="absolute inset-0 bg-gradient-to-t from-black/65 via-transparent to-transparent" />
        <span className="absolute top-3.5 left-3.5 z-10 font-display text-[9.5px] font-bold tracking-[.12em] uppercase bg-black/50 text-white/85 px-2.5 py-1 rounded-md backdrop-blur-sm">
          {story.region?.name}
        </span>
      </div>

      <div className="flex items-center gap-2 mb-2 font-display text-[10.5px] font-medium tracking-[.1em] uppercase text-ink-3">
        <span>{TYPE_LABEL[story.type] ?? story.type}</span>
        <span className="w-1 h-1 rounded-full bg-ink-3" />
        <span>{story.read_minutes} min read</span>
      </div>

      <h3 className={`font-serif font-bold leading-snug text-ink mb-2 ${featured ? 'text-[22px]' : 'text-[16px]'}`}>
        {story.title}
      </h3>

      {featured && (
        <p className="font-body text-[14px] leading-[1.7] text-ink-2 line-clamp-3">
          {story.excerpt}
        </p>
      )}
    </Link>
  );
}
