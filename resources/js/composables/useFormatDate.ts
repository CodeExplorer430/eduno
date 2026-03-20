export function useFormatDate(): { formatDate: (iso: string | null | undefined) => string } {
    function formatDate(iso: string | null | undefined): string {
        if (!iso) return '—';
        try {
            const date = new Date(iso);
            if (isNaN(date.getTime())) return '—';
            return new Intl.DateTimeFormat('en-PH', { dateStyle: 'medium' }).format(date);
        } catch {
            return '—';
        }
    }

    return { formatDate };
}
