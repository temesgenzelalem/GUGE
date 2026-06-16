'use client';
import { useState, useEffect, createContext, useContext, useCallback } from 'react';
import { X, CheckCircle, AlertCircle, Info } from 'lucide-react';
import { cn } from '@/lib/utils';

type ToastType = 'success' | 'error' | 'info';
interface Toast { id: string; message: string; type: ToastType }

const ToastContext = createContext<{
  addToast: (message: string, type?: ToastType) => void;
}>({ addToast: () => {} });

export function useToast() {
  return useContext(ToastContext);
}

export function ToastProvider({ children }: { children: React.ReactNode }) {
  const [toasts, setToasts] = useState<Toast[]>([]);

  const addToast = useCallback((message: string, type: ToastType = 'info') => {
    const id = Math.random().toString(36).slice(2);
    setToasts(prev => [...prev, { id, message, type }]);
    setTimeout(() => setToasts(prev => prev.filter(t => t.id !== id)), 4000);
  }, []);

  const remove = (id: string) => setToasts(prev => prev.filter(t => t.id !== id));

  return (
    <ToastContext.Provider value={{ addToast }}>
      {children}
      <div className="fixed bottom-6 right-6 z-[999] flex flex-col gap-2 max-w-sm">
        {toasts.map(toast => (
          <div
            key={toast.id}
            className={cn(
              'flex items-start gap-3 px-4 py-3.5 rounded-[12px] shadow-xl border text-[14px] font-body animate-fade-up',
              toast.type === 'success' && 'bg-forest-3 border-forest/20 text-forest',
              toast.type === 'error'   && 'bg-red-50 border-red-200 text-red-700',
              toast.type === 'info'    && 'bg-white border-black/10 text-ink',
            )}
          >
            {toast.type === 'success' && <CheckCircle size={18} className="mt-0.5 shrink-0" />}
            {toast.type === 'error'   && <AlertCircle  size={18} className="mt-0.5 shrink-0" />}
            {toast.type === 'info'    && <Info          size={18} className="mt-0.5 shrink-0" />}
            <span className="flex-1 leading-snug">{toast.message}</span>
            <button onClick={() => remove(toast.id)} className="mt-0.5 opacity-50 hover:opacity-100 transition-opacity">
              <X size={14} />
            </button>
          </div>
        ))}
      </div>
    </ToastContext.Provider>
  );
}
