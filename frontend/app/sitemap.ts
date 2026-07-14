import { MetadataRoute } from 'next';
import { getRegions, getProducts, getStories, getCreators, normalizeCollection } from '@/lib/api';
import type { Region, Product, Story, Creator } from '@/types';

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  const BASE = 'https://guge.et';

  const [regionsRes, productsRes, storiesRes, creatorsRes] = await Promise.all([
    getRegions({ per_page: 100 }).catch(() => ({ data: [] })),
    getProducts({ per_page: 100 }).catch(() => ({ data: [] })),
    getStories({ per_page: 100 }).catch(() => ({ data: [] })),
    getCreators({ per_page: 100 }).catch(() => ({ data: [] })),
  ]);

  const regions = normalizeCollection<Region>(regionsRes.data);
  const products = normalizeCollection<Product>(productsRes.data);
  const stories = normalizeCollection<Story>(storiesRes.data);
  const creators = normalizeCollection<Creator>(creatorsRes.data);

  const staticRoutes: MetadataRoute.Sitemap = [
    { url: BASE,                changeFrequency: 'daily',   priority: 1.0 },
    { url: `${BASE}/regions`,   changeFrequency: 'weekly',  priority: 0.9 },
    { url: `${BASE}/marketplace`, changeFrequency: 'daily', priority: 0.9 },
    { url: `${BASE}/stories`,   changeFrequency: 'daily',   priority: 0.8 },
    { url: `${BASE}/creators`,  changeFrequency: 'weekly',  priority: 0.7 },
    { url: `${BASE}/about`,     changeFrequency: 'monthly', priority: 0.5 },
    { url: `${BASE}/contact`,   changeFrequency: 'monthly', priority: 0.4 },
  ];

  return [
    ...staticRoutes,
    ...regions.map(r => ({ url: `${BASE}/regions/${r.slug}`, changeFrequency: 'weekly' as const, priority: 0.85 })),
    ...products.map(p => ({ url: `${BASE}/marketplace/${p.slug}`, changeFrequency: 'weekly' as const, priority: 0.75 })),
    ...stories.map(s => ({ url: `${BASE}/stories/${s.slug}`, changeFrequency: 'monthly' as const, priority: 0.7 })),
    ...creators.map(c => ({ url: `${BASE}/creators/${c.slug}`, changeFrequency: 'monthly' as const, priority: 0.6 })),
  ];
}
