import { ref } from 'vue';

export type ToastType = 'success' | 'error' | 'warning' | 'info';

interface Toast {
  id: number;
  message: string;
  type: ToastType;
}

const toasts = ref<Toast[]>([]);
let _nextId = 0;

export function useToast() {
  const show = (message: string, type: ToastType = 'info', duration = 3500) => {
    const id = _nextId++;
    toasts.value.push({ id, message, type });
    setTimeout(() => {
      toasts.value = toasts.value.filter((t) => t.id !== id);
    }, duration);
  };

  const success = (message: string) => show(message, 'success');
  const error   = (message: string) => show(message, 'error', 5000);
  const warning = (message: string) => show(message, 'warning');
  const info    = (message: string) => show(message, 'info');
  const dismiss = (id: number)      => { toasts.value = toasts.value.filter((t) => t.id !== id); };

  return { toasts, show, success, error, warning, info, dismiss };
}
