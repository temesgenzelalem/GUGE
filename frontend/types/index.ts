export interface Region {
  id: number;
  name: string;
  slug: string;
  zone: string;
  direction: 'north' | 'south' | 'east' | 'west';
  description: string;
  tagline: string;
  wiki_article: string;
  image_url: string | null;
  tags: string[];
  stats: RegionStat[];
  created_at: string;
  updated_at: string;
}

export interface RegionStat {
  label: string;
  value: string;
}

export interface Product {
  id: number;
  name: string;
  slug: string;
  region_id: number;
  region?: Region;
  category: 'coffee' | 'food' | 'craft' | 'honey' | 'clothing';
  description: string;
  story: string;
  wiki_article: string;
  image_url: string | null;
  tags: string[];
  how_to_order: string;
  created_at: string;
  updated_at: string;
}

export interface Story {
  id: number;
  title: string;
  slug: string;
  region_id: number;
  region?: Region;
  creator_id: number | null;
  creator?: Creator;
  type: 'travel' | 'product-origin' | 'culture' | 'festival' | 'history' | 'craft';
  excerpt: string;
  body: string;
  wiki_article: string;
  image_url: string | null;
  read_minutes: number;
  published_at: string;
  created_at: string;
  updated_at: string;
}

export interface Creator {
  id: number;
  name: string;
  slug: string;
  role: string;
  bio: string;
  region_coverage: string;
  wiki_article: string;
  image_url: string | null;
  contact_email: string | null;
  stories?: Story[];
  created_at: string;
  updated_at: string;
}

export interface PaginatedResponse<T> {
  current_page: number;
  data: T[];
  first_page_url: string;
  from: number | null;
  last_page: number;
  last_page_url: string;
  links: Array<{
    url: string | null;
    label: string;
    active: boolean;
  }>;
  next_page_url: string | null;
  path: string;
  per_page: number;
  prev_page_url: string | null;
  to: number | null;
  total: number;
}

export interface ApiResponse<T> {
  data: T;
  message?: string;
}

export interface ProductDetailResponse {
  data: Product;
  related: Product[];
}

export interface StoryDetailResponse {
  data: Story;
  related: Story[];
}
