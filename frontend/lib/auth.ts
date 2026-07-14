import { create } from 'zustand';
import { persist } from 'zustand/middleware';
import api, { getMe, login as loginRequest, register as registerRequest, logout as logoutRequest } from '@/lib/api';
import type { User } from '@/types';

interface AuthState {
  user: User | null;
  token: string | null;
  isAuthenticated: boolean;
  loading: boolean;
  login: (email: string, password: string) => Promise<void>;
  register: (name: string, email: string, password: string) => Promise<void>;
  logout: () => Promise<void>;
  hydrate: () => Promise<void>;
}

export const useAuthStore = create<AuthState>()(
  persist(
    (set, get) => ({
      user: null,
      token: null,
      isAuthenticated: false,
      loading: true,
      login: async (email, password) => {
        const res = await loginRequest({ email, password });
        const nextToken = res.token;
        set({ user: res.user, token: nextToken, isAuthenticated: true, loading: false });
        api.defaults.headers.common.Authorization = `Bearer ${nextToken}`;
      },
      register: async (name, email, password) => {
        const res = await registerRequest({ name, email, password, password_confirmation: password });
        const nextToken = res.token;
        set({ user: res.user, token: nextToken, isAuthenticated: true, loading: false });
        api.defaults.headers.common.Authorization = `Bearer ${nextToken}`;
      },
      logout: async () => {
        try {
          await logoutRequest();
        } catch {
          // ignore
        }
        set({ user: null, token: null, isAuthenticated: false, loading: false });
        delete api.defaults.headers.common.Authorization;
      },
      hydrate: async () => {
        const token = get().token;
        if (!token) {
          set({ loading: false });
          return;
        }
        api.defaults.headers.common.Authorization = `Bearer ${token}`;
        try {
          const res = await getMe();
          set({ user: res.user, isAuthenticated: true, loading: false });
        } catch {
          set({ user: null, token: null, isAuthenticated: false, loading: false });
          delete api.defaults.headers.common.Authorization;
        }
      },
    }),
    { name: 'guge-auth', partialize: (state) => ({ token: state.token, user: state.user, isAuthenticated: state.isAuthenticated }) },
  ),
);
