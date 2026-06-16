import { WikiImage } from '@/components/ui/WikiImage';

interface CulturalCardProps {
  icon?: string;
  title: string;
  type: string;
  wikiArticle: string;
  imageUrl?: string | null;
  onClick?: () => void;
}

export function CulturalCard({ title, type, wikiArticle, imageUrl, onClick }: CulturalCardProps) {
  return (
    <div
      className="group relative rounded-[18px] overflow-hidden h-[220px] cursor-pointer transition-transform duration-250 hover:-translate-y-1"
      onClick={onClick}
      role={onClick ? 'button' : undefined}
    >
      <WikiImage src={imageUrl ?? null} alt={title} dark />
      <div className="absolute inset-0 bg-gradient-to-t from-black/75 via-black/10 to-transparent" />
      <div className="absolute inset-0 p-4 flex flex-col justify-end z-10">
        <span className="font-display text-[9px] font-bold tracking-[.18em] uppercase text-white/55 mb-1">
          {type}
        </span>
        <h3 className="font-serif text-[19px] font-bold text-white leading-tight">
          {title}
        </h3>
      </div>
    </div>
  );
}
