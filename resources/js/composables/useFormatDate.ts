export function useFormatDate(): { formatDate: (iso: string | null | undefined) => string } {
    function formatDate(iso: string | null | undefined): string {
        if (!iso) return '—';
        return new Intl.DateTimeFormat('en-PH', { dateStyle: 'medium' }).format(new Date(iso));
    }

    return { formatDate };
}
