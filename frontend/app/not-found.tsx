import Link from 'next/link';

export default function NotFound() {
  return (
    <div className="min-h-[70vh] flex flex-col items-center justify-center px-6 text-center">
      <p className="font-display text-[10px] font-bold tracking-[.2em] uppercase text-forest mb-4">
        404 — Page not found
      </p>
      <h1 className="font-serif text-[64px] font-black tracking-tight text-ink leading-none mb-4">
        Lost in Ethiopia?
      </h1>
      <p className="font-body text-[17px] text-ink-3 max-w-md mb-10 leading-relaxed">
        The page you're looking for doesn't exist. Try exploring a region,
        browsing the marketplace, or reading a story.
      </p>
      <div className="flex flex-wrap gap-3 justify-center">
        <Link href="/"           className="font-display text-[12px] font-bold tracking-[.06em] uppercase px-6 py-3 bg-forest text-white rounded-md hover:bg-forest-2 transition-colors">Explore Ethiopia</Link>
        <Link href="/regions"    className="font-display text-[12px] font-bold tracking-[.06em] uppercase px-6 py-3 border border-black/12 text-ink-2 rounded-md hover:text-forest hover:border-forest transition-colors">All Regions</Link>
        <Link href="/marketplace" className="font-display text-[12px] font-bold tracking-[.06em] uppercase px-6 py-3 border border-black/12 text-ink-2 rounded-md hover:text-forest hover:border-forest transition-colors">Marketplace</Link>
      </div>
    </div>
  );
}
