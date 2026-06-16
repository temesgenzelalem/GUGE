'use client';
import Image from 'next/image';
import { useState } from 'react';
import { cn } from '@/lib/utils';
import { useWikiImage } from '@/lib/useWikiImage';

interface WikiImageProps {
  src: string | null;
  alt: string;
  className?: string;
  priority?: boolean;
  dark?: boolean;
  articleTitle?: string;
}

export function WikiImage({
  src,
  alt,
  className,
  priority = false,
  dark = false,
  articleTitle,
}: WikiImageProps) {
  const [loaded, setLoaded] = useState(false);
  const [error, setError] = useState(false);
  const { url: fallbackUrl } = useWikiImage(src ? null : articleTitle);
  const resolvedSrc = src ?? fallbackUrl;

  if (!resolvedSrc || error) {
    return (
      <div className={cn(dark ? 'shimmer-dark' : 'shimmer', 'w-full h-full', className)} />
    );
  }

  return (
    <div className={cn('relative w-full h-full', className)}>
      {!loaded && (
        <div className={cn('absolute inset-0', dark ? 'shimmer-dark' : 'shimmer')} />
      )}
      <Image
        src={resolvedSrc}
        alt={alt}
        fill
        className={cn('object-cover transition-opacity duration-500', loaded ? 'opacity-100' : 'opacity-0')}
        onLoad={() => setLoaded(true)}
        onError={() => setError(true)}
        priority={priority}
        sizes="(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw"
      />
    </div>
  );
}
