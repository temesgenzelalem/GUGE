interface Stat { label: string; value: string }

export function StatsBanner({ stats }: { stats: Stat[] }) {
  if (!stats || stats.length === 0) return null;

  return (
    <div className="grid grid-cols-2 sm:grid-cols-4 divide-x divide-black/8 border border-black/8 rounded-[16px] overflow-hidden bg-paper">
      {stats.slice(0, 4).map((stat) => (
        <div key={stat.label} className="px-5 py-5 text-center">
          <p className="font-serif text-[30px] font-bold text-forest mb-1">{stat.value}</p>
          <p className="font-display text-[9.5px] font-semibold tracking-[.1em] uppercase text-ink-3 leading-snug">
            {stat.label}
          </p>
        </div>
      ))}
    </div>
  );
}
