import Link from 'next/link';

interface SectionHeaderProps {
  eyebrow: string;
  title: string;
  linkLabel?: string;
  linkHref?: string;
  description?: string;
}

export function SectionHeader({
  eyebrow, title, linkLabel, linkHref, description,
}: SectionHeaderProps) {
  return (
    <div className="flex items-end justify-between mb-8 flex-wrap gap-4">
      <div>
        <p className="font-display text-[10px] font-bold tracking-[.2em] uppercase text-forest mb-2">
          {eyebrow}
        </p>
        <h2 className="font-serif text-[34px] font-bold tracking-tight leading-tight">
          {title}
        </h2>
        {description && (
          <p className="font-body text-[15px] text-ink-3 mt-2 max-w-xl">{description}</p>
        )}
      </div>
      {linkLabel && linkHref && (
        <Link
          href={linkHref}
          className="font-display text-[11px] font-bold tracking-[.14em] uppercase text-forest hover:opacity-70 transition-opacity whitespace-nowrap"
        >
          {linkLabel}
        </Link>
      )}
    </div>
  );
}
