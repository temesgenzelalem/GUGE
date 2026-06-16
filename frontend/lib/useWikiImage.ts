'use client';
import { useState, useEffect } from 'react';

const cache: Record<string, string | null> = {};

export function useWikiImage(articleTitle: string | null | undefined): {
  url: string | null;
  loading: boolean;
} {
  const [url,     setUrl]     = useState<string | null>(cache[articleTitle ?? ''] ?? null);
  const [loading, setLoading] = useState(!cache[articleTitle ?? '']);

  useEffect(() => {
    if (!articleTitle) { setLoading(false); return; }
    if (cache[articleTitle] !== undefined) {
      setUrl(cache[articleTitle]);
      setLoading(false);
      return;
    }

    let cancelled = false;
    setLoading(true);

    fetch(
      `https://en.wikipedia.org/w/api.php?action=query&titles=${encodeURIComponent(articleTitle)}&prop=pageimages&pithumbsize=900&format=json&origin=*`,
    )
      .then(r => r.json())
      .then(data => {
        if (cancelled) return;
        const pages = data.query?.pages ?? {};
        const page  = Object.values(pages)[0] as any;
        const src   = page?.thumbnail?.source ?? null;
        cache[articleTitle] = src;
        setUrl(src);
      })
      .catch(() => {
        if (!cancelled) { cache[articleTitle] = null; setUrl(null); }
      })
      .finally(() => { if (!cancelled) setLoading(false); });

    return () => { cancelled = true; };
  }, [articleTitle]);

  return { url, loading };
}
