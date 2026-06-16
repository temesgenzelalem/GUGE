import Link from 'next/link';

const NAV = [
  { label: 'Explore',     href: '/'           },
  { label: 'All Regions', href: '/regions'    },
  { label: 'Marketplace', href: '/marketplace'},
  { label: 'Stories',     href: '/stories'    },
  { label: 'Creators',    href: '/creators'   },
  { label: 'About',       href: '/about'      },
  { label: 'Contact',     href: '/contact'    },
];

export function Footer() {
  return (
    <footer className="bg-ink text-white">
      <div className="px-10 py-14 grid grid-cols-1 md:grid-cols-[auto_1fr_auto] gap-10 items-start border-b border-white/8">
        {/* Brand */}
        <div className="max-w-xs">
          <Link href="/" className="font-serif text-[26px] font-black flex items-center gap-1.5 mb-3">
            GUGE
            <span className="w-2 h-2 rounded-full bg-forest-2 inline-block mb-0.5" />
          </Link>
          <p className="font-body text-[14px] italic text-white/40 mb-4">
            "Travel Ethiopia. Buy Ethiopia."
          </p>
          <p className="font-body text-[13.5px] leading-relaxed text-white/35">
            A digital gateway to Ethiopian culture, travel, and authentic local
            products — connecting places, stories, and communities.
          </p>
        </div>

        {/* Nav links */}
        <div className="md:pl-20">
          <p className="font-display text-[9.5px] font-bold tracking-[.2em] uppercase text-white/30 mb-4">
            Navigate
          </p>
          <ul className="grid grid-cols-2 gap-x-10 gap-y-2.5">
            {NAV.map(({ label, href }) => (
              <li key={href}>
                <Link
                  href={href}
                  className="font-display text-[11px] font-semibold tracking-[.08em] uppercase text-white/45 hover:text-gold transition-colors"
                >
                  {label}
                </Link>
              </li>
            ))}
          </ul>
        </div>

        {/* Newsletter */}
        <div className="max-w-xs">
          <p className="font-display text-[9.5px] font-bold tracking-[.2em] uppercase text-white/30 mb-4">
            Stay connected
          </p>
          <p className="font-body text-[13px] text-white/35 mb-4">
            Get new region stories and product launches straight to your inbox.
          </p>
          <NewsletterForm />
        </div>
      </div>

      {/* Bottom bar */}
      <div className="px-10 py-5 flex flex-col sm:flex-row items-center justify-between gap-3">
        <p className="font-display text-[10px] font-medium tracking-[.08em] text-white/25">
          © {new Date().getFullYear()} GUGE Platform. Built in Ethiopia.
        </p>
        <div className="flex gap-5">
          {['Privacy', 'Terms', 'Cookies'].map((t) => (
            <Link key={t} href="#" className="font-display text-[10px] tracking-[.08em] text-white/25 hover:text-white/50 transition-colors">
              {t}
            </Link>
          ))}
        </div>
      </div>
    </footer>
  );
}

function NewsletterForm() {
  return (
    <form
      action="/api/newsletter"
      method="POST"
      className="flex gap-2"
    >
      <input
        type="email"
        required
        placeholder="your@email.com"
        className="flex-1 bg-white/8 border border-white/15 rounded-md px-3 py-2.5 font-body text-[13px] text-white placeholder:text-white/30 outline-none focus:border-gold/60 transition-colors min-w-0"
      />
      <button
        type="submit"
        className="font-display text-[10px] font-bold tracking-[.1em] uppercase px-4 py-2.5 bg-forest rounded-md text-white hover:bg-forest-2 transition-colors whitespace-nowrap"
      >
        Subscribe
      </button>
    </form>
  );
}
