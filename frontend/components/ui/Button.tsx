import { cn } from '@/lib/utils';
import Link from 'next/link';
import { Loader2 } from 'lucide-react';

type Variant = 'primary' | 'outline' | 'ghost' | 'dark';
type Size    = 'sm' | 'md' | 'lg';

interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  variant?: Variant;
  size?: Size;
  loading?: boolean;
  href?: string;
}

const variantCls: Record<Variant, string> = {
  primary: 'bg-forest text-white hover:bg-forest-2 shadow-sm hover:shadow-md',
  outline: 'border border-black/14 text-ink-2 hover:border-forest hover:text-forest',
  ghost:   'text-ink-2 hover:text-forest',
  dark:    'bg-ink text-white hover:bg-ink-2',
};

const sizeCls: Record<Size, string> = {
  sm: 'text-[10.5px] px-4 py-2',
  md: 'text-[12px] px-6 py-3',
  lg: 'text-[13px] px-8 py-4',
};

export function Button({
  variant = 'primary', size = 'md', loading, href, className, children, disabled, ...props
}: ButtonProps) {
  const base = cn(
    'inline-flex items-center justify-center gap-2 font-display font-bold tracking-[.07em] uppercase rounded-md transition-all cursor-pointer',
    variantCls[variant],
    sizeCls[size],
    (loading || disabled) && 'opacity-60 cursor-not-allowed pointer-events-none',
    className,
  );

  if (href) {
    return <Link href={href} className={base}>{children}</Link>;
  }

  return (
    <button className={base} disabled={disabled || loading} {...props}>
      {loading && <Loader2 size={14} className="animate-spin" />}
      {children}
    </button>
  );
}
