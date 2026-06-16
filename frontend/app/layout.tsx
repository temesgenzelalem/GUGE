import type { Metadata, Viewport } from 'next';
import './globals.css';
import { Navbar } from '@/components/layout/Navbar';
import { Footer } from '@/components/layout/Footer';
import { Providers } from '@/components/layout/Providers';

export const metadata: Metadata = {
  metadataBase: new URL('https://guge.et'),
  title: {
    default: 'GUGE — Travel Ethiopia. Buy Ethiopia.',
    template: '%s — GUGE',
  },
  description:
    'A digital gateway to Ethiopian culture, travel, and authentic local products. Explore 22 regions, discover stories, and shop from local artisans and farmers.',
  keywords: ['Ethiopia travel','Ethiopian products','Lalibela','Ethiopian coffee','Harar','Gondar','Yirgacheffe','Ethiopian culture','buy Ethiopian'],
  openGraph: {
    title: 'GUGE — Travel Ethiopia. Buy Ethiopia.',
    description: 'A digital gateway to Ethiopian culture, travel, and authentic local products.',
    url: 'https://guge.et',
    siteName: 'GUGE',
    type: 'website',
    locale: 'en_US',
  },
  twitter: { card: 'summary_large_image', title: 'GUGE — Travel Ethiopia. Buy Ethiopia.' },
  manifest: '/manifest.json',
  robots: { index: true, follow: true },
};

export const viewport: Viewport = {
  themeColor: '#0d5c43',
  width: 'device-width',
  initialScale: 1,
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en">
      <body className="antialiased">
        <Providers>
          <Navbar />
          <main className="pt-[60px] min-h-screen">{children}</main>
          <Footer />
        </Providers>
      </body>
    </html>
  );
}
