'use client';
import { useState, useEffect, useRef } from 'react';
import { globalSearch } from '@/lib/api';
import type { Region, Product, Story } from '@/types';

interface SearchResults {
  regions:  Region[];
  products: Product[];
  stories:  Story[];
}

export function useSearch(query: string, debounceMs = 320) {
  const [results, setResults] = useState<SearchResults | null>(null);
  const [loading, setLoading] = useState(false);
  const [error,   setError]   = useState<string | null>(null);
  const timer = useRef<ReturnType<typeof setTimeout> | null>(null);

  useEffect(() => {
    if (timer.current) clearTimeout(timer.current);

    if (!query || query.length < 2) {
      setResults(null);
      setLoading(false);
      return;
    }

    setLoading(true);
    setError(null);

    timer.current = setTimeout(async () => {
      try {
        const data = await globalSearch(query);
        setResults(data);
      } catch {
        setError('Search failed. Please try again.');
        setResults(null);
      } finally {
        setLoading(false);
      }
    }, debounceMs);

    return () => { if (timer.current) clearTimeout(timer.current); };
  }, [query, debounceMs]);

  const total = results
    ? results.regions.length + results.products.length + results.stories.length
    : 0;

  return { results, loading, error, total };
}
