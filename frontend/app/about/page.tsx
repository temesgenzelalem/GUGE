import type { Metadata } from 'next';
import Link from 'next/link';

export const metadata: Metadata = {
  title: 'About — GUGE',
  description: 'GUGE connects Ethiopian places, culture, and authentic local products through region-based discovery.',
};

const VISION_POINTS = [
  { icon: '🗺️', title: 'Region as the core', body: 'Everything on GUGE connects to a region. Not a category, not a keyword — a place with a name, a culture, a geography, and a story.' },
  { icon: '📖', title: 'Content is the product', body: 'Stories, photography, and cultural knowledge are not marketing — they are the platform itself. Content that grows forever and is impossible to copy.' },
  { icon: '🤝', title: 'Products invite travel', body: 'A Yirgacheffe coffee becomes a reason to visit. A Harari basket becomes a connection to a place. Commerce and tourism feed each other.' },
  { icon: '🌍', title: 'Built for the diaspora first', body: 'Ethiopians abroad carry deep emotional connections to home. GUGE gives them a way to explore, buy, share, and stay connected to where they come from.' },
  { icon: '👩‍🎨', title: 'Creators power the network', body: 'Photographers, writers, weavers, and farmers are the real experts. GUGE gives them visibility, attribution, and eventually revenue.' },
  { icon: '📈', title: 'Local economy first', body: 'Every purchase connects to a real person in a real region. No middlemen, no big brands — only artisans, farmers, and cooperatives.' },
];

const PHASES = [
  { phase: '01', title: 'Discover Ethiopia', status: 'Now', color: 'bg-forest', desc: 'Region pages, cultural stories, product showcases, and creator profiles. Content-first. No checkout yet.' },
  { phase: '02', title: 'Regional Commerce', status: 'Next', color: 'bg-amber', desc: 'Seller dashboards, online payments, verified local producers, regional storefronts and reviews.' },
  { phase: '03', title: 'Full Ecosystem', status: 'Future', color: 'bg-[#533ab7]', desc: 'Booking, local guides, creator economy, cultural subscriptions, experience packages, export marketplace.' },
];

export default function AboutPage() {
  return (
    <div className="max-w-[900px] mx-auto px-6 py-16">

      {/* Hero */}
      <div className="mb-16">
        <p className="font-display text-[10px] font-bold tracking-[.22em] uppercase text-forest mb-5 flex items-center gap-3">
          <span className="w-7 h-px bg-forest" /> Our mission
        </p>
        <h1 className="font-serif text-[clamp(44px,7vw,72px)] font-black tracking-[-2.5px] leading-[.92] mb-8">
          A digital gateway<br />
          to <em className="font-serif italic font-normal text-forest">Ethiopian</em><br />
          culture & commerce
        </h1>
        <p className="font-body text-[18px] leading-[1.8] text-ink-2 max-w-2xl">
          GUGE is not just a travel website. It is not just an ecommerce platform.
          It is a region-based cultural discovery platform that connects Ethiopian
          places, traditions, and authentic local products through storytelling,
          geography, and community.
        </p>
      </div>

      <hr className="border-black/8 mb-14" />

      {/* The name */}
      <section className="mb-14">
        <h2 className="font-serif text-[32px] font-bold mb-6">The name</h2>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
          {[['GUZO', 'Travel — discovering places, cultures, and journeys across Ethiopia.'], ['GEBEYA', 'Market — authentic local products, linked to where they come from.']].map(([word, def]) => (
            <div key={word} className="p-6 bg-paper-2 rounded-[18px] border border-black/8">
              <p className="font-serif text-[32px] font-bold text-forest mb-2">{word}</p>
              <p className="font-body text-[15px] text-ink-2 leading-relaxed">{def}</p>
            </div>
          ))}
        </div>
        <p className="font-body text-[16px] leading-[1.75] text-ink-2">
          Combined into <strong>GUGE</strong> — a platform where travel and marketplace are the
          visible parts, but the real core is connecting Ethiopian identity to place.
          Every place has a story. Every product has an origin. GUGE connects them.
        </p>
      </section>

      {/* Vision pillars */}
      <section className="mb-14">
        <h2 className="font-serif text-[32px] font-bold mb-8">How we think about it</h2>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
          {VISION_POINTS.map(({ icon, title, body }) => (
            <div key={title} className="p-6 bg-paper rounded-[18px] border border-black/8">
              <div className="text-[28px] mb-4">{icon}</div>
              <h3 className="font-serif text-[19px] font-bold text-ink mb-2">{title}</h3>
              <p className="font-body text-[14px] leading-[1.7] text-ink-3">{body}</p>
            </div>
          ))}
        </div>
      </section>

      {/* Roadmap */}
      <section className="mb-14">
        <h2 className="font-serif text-[32px] font-bold mb-8">The build sequence</h2>
        <div className="space-y-4">
          {PHASES.map(({ phase, title, status, color, desc }) => (
            <div key={phase} className="flex gap-5 p-6 bg-paper-2 rounded-[18px] border border-black/8 items-start">
              <div className={`${color} text-white font-display text-[11px] font-bold tracking-[.1em] px-3 py-1.5 rounded-full whitespace-nowrap mt-0.5`}>
                {status}
              </div>
              <div>
                <p className="font-display text-[9.5px] font-bold tracking-[.15em] uppercase text-ink-3 mb-1">Phase {phase}</p>
                <h3 className="font-serif text-[20px] font-bold text-ink mb-1">{title}</h3>
                <p className="font-body text-[14px] leading-[1.7] text-ink-3">{desc}</p>
              </div>
            </div>
          ))}
        </div>
      </section>

      {/* CTA */}
      <div className="bg-ink rounded-[24px] p-10 text-center">
        <h2 className="font-serif text-[32px] font-bold text-white mb-3">Start exploring</h2>
        <p className="font-body text-[16px] text-white/50 mb-7 max-w-md mx-auto">
          22 regions. 32 products. 12 long-form stories. 8 creators. All connected to where they come from.
        </p>
        <div className="flex flex-wrap gap-3 justify-center">
          <Link href="/regions" className="font-display text-[12px] font-bold tracking-[.06em] uppercase px-6 py-3 bg-forest text-white rounded-md hover:bg-forest-2 transition-colors">
            Browse regions →
          </Link>
          <Link href="/marketplace" className="font-display text-[12px] font-bold tracking-[.06em] uppercase px-6 py-3 border border-white/20 text-white/70 rounded-md hover:border-gold hover:text-gold transition-colors">
            Shop local products
          </Link>
        </div>
      </div>
    </div>
  );
}
