import { describe, it, expect } from 'vitest';
import { useFormatDate } from '@/composables/useFormatDate';

describe('useFormatDate', () => {
    const { formatDate } = useFormatDate();

    it('returns em-dash for null', () => {
        expect(formatDate(null)).toBe('—');
    });

    it('returns em-dash for undefined', () => {
        expect(formatDate(undefined)).toBe('—');
    });

    it('returns em-dash for empty string', () => {
        expect(formatDate('')).toBe('—');
    });

    it('formats a valid ISO date string in en-PH medium format', () => {
        const result = formatDate('2025-03-15');
        // en-PH medium date: "Mar 15, 2025" or similar locale output
        expect(result).toMatch(/Mar/);
        expect(result).toMatch(/2025/);
        expect(result).toMatch(/15/);
    });

    it('handles an invalid date string gracefully without throwing', () => {
        expect(() => formatDate('not-a-date')).not.toThrow();
    });
});
