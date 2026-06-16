import axios from 'axios';
import type {
  Region, Product, Story, Creator,
  PaginatedResponse, ApiResponse, ProductDetailResponse, StoryDetailResponse,
} from '@/types';

const api = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_URL ?? 'http://localhost:8000/api',
  headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
});

// ── REGIONS ─────────────────────────────────────────────────
export const getRegions = async (params?: {
  direction?: string;
  search?: string;
  page?: number;
  per_page?: number;
}): Promise<PaginatedResponse<Region>> => {
  const { data } = await api.get('/regions', { params });
  return data;
};

export const getRegion = async (slug: string): Promise<ApiResponse<Region>> => {
  const { data } = await api.get(`/regions/${slug}`);
  return data;
};

export const getRegionProducts = async (slug: string): Promise<ApiResponse<Product[]>> => {
  const { data } = await api.get(`/regions/${slug}/products`);
  return data;
};

export const getRegionStories = async (slug: string): Promise<ApiResponse<Story[]>> => {
  const { data } = await api.get(`/regions/${slug}/stories`);
  return data;
};

// ── PRODUCTS ─────────────────────────────────────────────────
export const getProducts = async (params?: {
  category?: string;
  region_id?: number;
  search?: string;
  page?: number;
  per_page?: number;
}): Promise<PaginatedResponse<Product>> => {
  const { data } = await api.get('/products', { params });
  return data;
};

export const getProduct = async (slug: string): Promise<ProductDetailResponse> => {
  const { data } = await api.get(`/products/${slug}`);
  return data;
};

// ── STORIES ──────────────────────────────────────────────────
export const getStories = async (params?: {
  type?: string;
  region_id?: number;
  page?: number;
  per_page?: number;
}): Promise<PaginatedResponse<Story>> => {
  const { data } = await api.get('/stories', { params });
  return data;
};

export const getStory = async (slug: string): Promise<StoryDetailResponse> => {
  const { data } = await api.get(`/stories/${slug}`);
  return data;
};

// ── CREATORS ─────────────────────────────────────────────────
export const getCreators = async (params?: {
  page?: number;
  per_page?: number;
}): Promise<PaginatedResponse<Creator>> => {
  const { data } = await api.get('/creators', { params });
  return data;
};

export const getCreator = async (slug: string): Promise<ApiResponse<Creator>> => {
  const { data } = await api.get(`/creators/${slug}`);
  return data;
};

// ── SEARCH ───────────────────────────────────────────────────
export const globalSearch = async (q: string): Promise<{
  regions: Region[];
  products: Product[];
  stories: Story[];
}> => {
  const { data } = await api.get('/search', { params: { q } });
  return data;
};

export default api;
