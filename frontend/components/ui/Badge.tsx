import { cn } from '@/lib/utils';

type BadgeVariant = 'forest' | 'amber' | 'gold' | 'ink' | 'paper';

interface BadgeProps {
  label: string;
  variant?: BadgeVariant;
  className?: string;
}

const variantCls: Record<BadgeVariant, string> = {
  forest: 'bg-forest-3 text-forest',
  amber:  'bg-amber/10 text-amber',
  gold:   'bg-gold/10  text-gold',
  ink:    'bg-ink text-white',
  paper:  'bg-paper-2 text-ink-3 border border-black/10',
};

export function Badge({ label, variant = 'forest', className }: BadgeProps) {
  return (
    <span
      className={cn(
        'inline-block font-display text-[9px] font-bold tracking-[.12em] uppercase px-2.5 py-1 rounded-full',
        variantCls[variant],
        className,
      )}
    >
      {label}
    </span>
  );
}
