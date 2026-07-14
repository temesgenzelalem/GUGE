import axios from 'axios';
import type {
  Region, Product, Story, Creator, User,
  PaginatedResponse, ApiResponse, ProductDetailResponse, StoryDetailResponse,
} from '@/types';

const api = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_URL ?? 'http://localhost:8000/api',
  headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
});

const isServer = typeof window === 'undefined';

const withFallback = async <T>(request: () => Promise<T>, fallback: T): Promise<T> => {
  try {
    return await request();
  } catch (err) {
    // Log a lightweight warning for easier debugging during development
    // Keep returning the fallback so builds and prerendering don't fail
    // eslint-disable-next-line no-console
    console.warn('[api] request failed, returning fallback', err);
    return fallback;
  }
};

export const normalizeCollection = <T = unknown>(value: unknown): T[] => {
  if (Array.isArray(value)) return value as T[];
  if (value == null) return [];
  if (typeof value === 'object') {
    const record = value as Record<string, unknown>;
    if (Array.isArray(record.data)) return record.data as T[];
    if (Array.isArray(record.items)) return record.items as T[];
    if (record.data && typeof record.data === 'object') {
      const nested = record.data as Record<string, unknown>;
      if (Array.isArray(nested.data)) return nested.data as T[];
      if (Array.isArray(nested.items)) return nested.items as T[];
    }
  }
  return [];
};

export const normalizePayload = <T = unknown>(payload: unknown): { data: T[]; meta?: Record<string, unknown> } => {
  if (payload && typeof payload === 'object') {
    const value = payload as Record<string, unknown>;
    if (Array.isArray(value)) {
      return { data: value as T[] };
    }
    if (value.data && typeof value.data === 'object') {
      const nested = value.data as Record<string, unknown>;
      if (Array.isArray(nested.data)) {
        return { data: nested.data as T[], meta: value as Record<string, unknown> };
      }
      if (Array.isArray(nested.items)) {
        return { data: nested.items as T[], meta: value as Record<string, unknown> };
      }
    }
    if (Array.isArray(value.data)) {
      return { data: value.data as T[], meta: value as Record<string, unknown> };
    }
    if (Array.isArray(value.items)) {
      return { data: value.items as T[], meta: value as Record<string, unknown> };
    }
  }
  return { data: [] };
};

// ── REGIONS ─────────────────────────────────────────────────
export const getRegions = async (params?: {
  direction?: string;
  search?: string;
  page?: number;
  per_page?: number;
}): Promise<PaginatedResponse<Region>> => {
  return withFallback(async () => {
    const { data } = await api.get('/regions', { params });
    const normalized = normalizePayload<Region>(data);
    return {
      ...((data && typeof data === 'object' && !Array.isArray(data)) ? data : {}),
      data: normalized.data,
      current_page: data?.current_page ?? 1,
      per_page: data?.per_page ?? normalized.data.length,
      total: data?.total ?? normalized.data.length,
      last_page: data?.last_page ?? 1,
    } as PaginatedResponse<Region>;
  }, { data: [], current_page: 1, first_page_url: '', from: null, last_page: 1, last_page_url: '', links: [], next_page_url: null, path: '', per_page: 10, prev_page_url: null, to: null, total: 0 });
};

export const getRegion = async (slug: string): Promise<ApiResponse<Region>> => {
  return withFallback(async () => {
    const { data } = await api.get(`/regions/${slug}`);
    return data;
  }, { data: { id: 0, name: 'Region unavailable', slug, zone: '', direction: 'north', description: 'The region data is temporarily unavailable.', tagline: '', wiki_article: '', image_url: null, tags: [], stats: [], created_at: '', updated_at: '' } });
};

export const getRegionProducts = async (slug: string): Promise<ApiResponse<Product[]>> => {
  return withFallback(async () => {
    const { data } = await api.get(`/regions/${slug}/products`);
    return { data: normalizeCollection<Product>(data) } as ApiResponse<Product[]>;
  }, { data: [] });
};

export const getRegionStories = async (slug: string): Promise<ApiResponse<Story[]>> => {
  return withFallback(async () => {
    const { data } = await api.get(`/regions/${slug}/stories`);
    return { data: normalizeCollection<Story>(data) } as ApiResponse<Story[]>;
  }, { data: [] });
};

// ── PRODUCTS ─────────────────────────────────────────────────
export const getProducts = async (params?: {
  category?: string;
  region_id?: number;
  search?: string;
  page?: number;
  per_page?: number;
}): Promise<PaginatedResponse<Product>> => {
  return withFallback(async () => {
    const { data } = await api.get('/products', { params });
    const normalized = normalizePayload<Product>(data);
    return {
      ...((data && typeof data === 'object' && !Array.isArray(data)) ? data : {}),
      data: normalized.data,
      current_page: data?.current_page ?? 1,
      per_page: data?.per_page ?? normalized.data.length,
      total: data?.total ?? normalized.data.length,
      last_page: data?.last_page ?? 1,
    } as PaginatedResponse<Product>;
  }, { data: [], current_page: 1, first_page_url: '', from: null, last_page: 1, last_page_url: '', links: [], next_page_url: null, path: '', per_page: 10, prev_page_url: null, to: null, total: 0 });
};

export const getProduct = async (slug: string): Promise<ProductDetailResponse> => {
  return withFallback(async () => {
    const { data } = await api.get(`/products/${slug}`);
    return data;
  }, { data: { id: 0, name: 'Product unavailable', slug, region_id: 0, category: 'craft', description: 'This product is temporarily unavailable.', story: '', wiki_article: '', image_url: null, tags: [], how_to_order: 'Please check back soon.', created_at: '', updated_at: '' }, related: [] });
};

// ── STORIES ──────────────────────────────────────────────────
export const getStories = async (params?: {
  type?: string;
  region_id?: number;
  page?: number;
  per_page?: number;
}): Promise<PaginatedResponse<Story>> => {
  return withFallback(async () => {
    const { data } = await api.get('/stories', { params });
    const normalized = normalizePayload<Story>(data);
    return {
      ...((data && typeof data === 'object' && !Array.isArray(data)) ? data : {}),
      data: normalized.data,
      current_page: data?.current_page ?? 1,
      per_page: data?.per_page ?? normalized.data.length,
      total: data?.total ?? normalized.data.length,
      last_page: data?.last_page ?? 1,
    } as PaginatedResponse<Story>;
  }, { data: [], current_page: 1, first_page_url: '', from: null, last_page: 1, last_page_url: '', links: [], next_page_url: null, path: '', per_page: 10, prev_page_url: null, to: null, total: 0 });
};

export const getStory = async (slug: string): Promise<StoryDetailResponse> => {
  return withFallback(async () => {
    const { data } = await api.get(`/stories/${slug}`);
    return data;
  }, { data: { id: 0, title: 'Story unavailable', slug, region_id: 0, type: 'culture', excerpt: 'This story is temporarily unavailable.', body: '', wiki_article: '', image_url: null, read_minutes: 3, published_at: '', created_at: '', updated_at: '' }, related: [] });
};

// ── CREATORS ─────────────────────────────────────────────────
export const getCreators = async (params?: {
  page?: number;
  per_page?: number;
}): Promise<PaginatedResponse<Creator>> => {
  return withFallback(async () => {
    const { data } = await api.get('/creators', { params });
    const normalized = normalizePayload<Creator>(data);
    return {
      ...((data && typeof data === 'object' && !Array.isArray(data)) ? data : {}),
      data: normalized.data,
      current_page: data?.current_page ?? 1,
      per_page: data?.per_page ?? normalized.data.length,
      total: data?.total ?? normalized.data.length,
      last_page: data?.last_page ?? 1,
    } as PaginatedResponse<Creator>;
  }, { data: [], current_page: 1, first_page_url: '', from: null, last_page: 1, last_page_url: '', links: [], next_page_url: null, path: '', per_page: 10, prev_page_url: null, to: null, total: 0 });
};

export const getCreator = async (slug: string): Promise<ApiResponse<Creator>> => {
  return withFallback(async () => {
    const { data } = await api.get(`/creators/${slug}`);
    return data;
  }, { data: { id: 0, name: 'Creator unavailable', slug, role: 'Contributor', bio: 'This creator profile is temporarily unavailable.', region_coverage: '', wiki_article: '', image_url: null, contact_email: null, created_at: '', updated_at: '' } });
};

// ── SEARCH ───────────────────────────────────────────────────
export const globalSearch = async (q: string): Promise<{
  regions: Region[];
  products: Product[];
  stories: Story[];
}> => {
  return withFallback(async () => {
    const { data } = await api.get('/search', { params: { q } });
    if (data && typeof data === 'object' && !Array.isArray(data)) {
      return {
        regions: normalizeCollection<Region>(data.regions),
        products: normalizeCollection<Product>(data.products),
        stories: normalizeCollection<Story>(data.stories),
      };
    }
    return { regions: [], products: [], stories: [] };
  }, { regions: [], products: [], stories: [] });
};

// ── AUTH ─────────────────────────────────────────────────────
export const login = async (payload: { email: string; password: string }) => {
  const { data } = await api.post('/login', payload);
  return data.data as { user: User; token: string };
};

export const register = async (payload: { name: string; email: string; password: string; password_confirmation: string }) => {
  const { data } = await api.post('/register', payload);
  return data.data as { user: User; token: string };
};

export const getMe = async () => {
  const { data } = await api.get('/me');
  return data.data as { user: User };
};

export const logout = async () => {
  const { data } = await api.post('/logout');
  return data;
};

if (!isServer && typeof window !== 'undefined') {
  const saved = window.localStorage.getItem('guge-auth');
  if (saved) {
    try {
      const parsed = JSON.parse(saved);
      const token = parsed?.state?.token;
      if (token) {
        api.defaults.headers.common.Authorization = `Bearer ${token}`;
      }
    } catch {
      // ignore
    }
  }
}

export default api;
