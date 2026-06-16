export const SITE_NAME = 'GUGE';
export const SITE_URL  = 'https://guge.et';
export const SITE_DESC = 'A digital gateway to Ethiopian culture, travel, and authentic local products.';

export const CATEGORIES = [
  { value: 'coffee',   label: 'Coffee'        },
  { value: 'food',     label: 'Food & Spice'  },
  { value: 'craft',    label: 'Craft'         },
  { value: 'honey',    label: 'Honey & Tej'   },
  { value: 'clothing', label: 'Clothing'      },
] as const;

export const DIRECTIONS = [
  { value: 'north', label: 'North' },
  { value: 'south', label: 'South' },
  { value: 'east',  label: 'East'  },
  { value: 'west',  label: 'West'  },
] as const;

export const STORY_TYPES = [
  { value: 'travel',         label: 'Travel'         },
  { value: 'product-origin', label: 'Product Origin' },
  { value: 'culture',        label: 'Culture'        },
  { value: 'festival',       label: 'Festival'       },
  { value: 'history',        label: 'History'        },
  { value: 'craft',          label: 'Craft'          },
] as const;

export const CATEGORY_EMOJI: Record<string, string> = {
  coffee:   '☕',
  food:     '🌾',
  craft:    '🧺',
  honey:    '🍯',
  clothing: '👘',
};

export const DIRECTION_EMOJI: Record<string, string> = {
  north: '⬆️',
  south: '⬇️',
  east:  '➡️',
  west:  '⬅️',
};
