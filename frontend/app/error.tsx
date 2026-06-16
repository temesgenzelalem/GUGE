'use client';
import { useEffect } from 'react';
import Link from 'next/link';

export default function Error({
  error,
  reset,
}: {
  error: Error & { digest?: string };
  reset: () => void;
}) {
  useEffect(() => {
    console.error(error);
  }, [error]);

  return (
    <div className="min-h-[60vh] flex flex-col items-center justify-center px-6 text-center">
      <p className="font-display text-[10px] font-bold tracking-[.2em] uppercase text-forest mb-4">
        Something went wrong
      </p>
      <h1 className="font-serif text-[48px] font-bold tracking-tight text-ink mb-4">
        Unexpected error
      </h1>
      <p className="font-body text-[16px] text-ink-3 max-w-md mb-8">
        We couldn't load this page. This may be a temporary connection issue with
        the API or database.
      </p>
      <div className="flex gap-4">
        <button
          onClick={reset}
          className="font-display text-[12px] font-bold tracking-[.06em] uppercase px-6 py-3 bg-forest text-white rounded-md hover:bg-forest-2 transition-colors"
        >
          Try again
        </button>
        <Link
          href="/"
          className="font-display text-[12px] font-bold tracking-[.06em] uppercase px-6 py-3 border border-black/12 text-ink-2 rounded-md hover:border-forest hover:text-forest transition-colors"
        >
          Go home
        </Link>
      </div>
    </div>
  );
}
