import { describe, it, expect } from 'vitest';
import { useFileSize } from '@/composables/useFileSize';

describe('useFileSize', () => {
    const { formatBytes } = useFileSize();

    it('formats 0 bytes as "0 B"', () => {
        expect(formatBytes(0)).toBe('0 B');
    });

    it('formats 512 bytes as "512 B"', () => {
        expect(formatBytes(512)).toBe('512 B');
    });

    it('formats 1023 bytes as "1023 B"', () => {
        expect(formatBytes(1023)).toBe('1023 B');
    });

    it('formats 1024 bytes as "1.0 KB"', () => {
        expect(formatBytes(1024)).toBe('1.0 KB');
    });

    it('formats 1048576 bytes (1 MB) as "1.0 MB"', () => {
        expect(formatBytes(1024 * 1024)).toBe('1.0 MB');
    });

    it('formats 2621440 bytes as "2.5 MB"', () => {
        expect(formatBytes(2.5 * 1024 * 1024)).toBe('2.5 MB');
    });
});
