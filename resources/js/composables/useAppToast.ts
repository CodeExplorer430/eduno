import { useToast } from 'primevue/usetoast';

export function useAppToast(): {
    success: (msg: string) => void;
    error: (msg: string) => void;
    info: (msg: string) => void;
    warn: (msg: string) => void;
} {
    const toast = useToast();
    return {
        success: (msg: string): void =>
            toast.add({ severity: 'success', summary: 'Success', detail: msg, life: 3500 }),
        error: (msg: string): void =>
            toast.add({ severity: 'error', summary: 'Error', detail: msg, life: 5000 }),
        info: (msg: string): void =>
            toast.add({ severity: 'info', summary: 'Info', detail: msg, life: 3500 }),
        warn: (msg: string): void =>
            toast.add({ severity: 'warn', summary: 'Warning', detail: msg, life: 4000 }),
    };
}
