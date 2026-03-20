import { describe, it, expect, vi, beforeEach } from 'vitest';
import { useOfflineCache } from '@/composables/useOfflineCache';

function makeMockCache() {
    const store = new Map<string, Response>();
    return {
        match: vi.fn((url: string) => Promise.resolve(store.get(url))),
        add: vi.fn((url: string) => {
            store.set(url, new Response());
            return Promise.resolve();
        }),
        put: vi.fn(),
        delete: vi.fn(),
        keys: vi.fn(() => Promise.resolve([])),
    };
}

function makeMockCacheStorage(cache: ReturnType<typeof makeMockCache>) {
    return {
        open: vi.fn(() => Promise.resolve(cache)),
        has: vi.fn(),
        delete: vi.fn(),
        keys: vi.fn(),
        match: vi.fn(),
    };
}

describe('useOfflineCache', () => {
    let mockCache: ReturnType<typeof makeMockCache>;

    beforeEach(() => {
        mockCache = makeMockCache();
        vi.stubGlobal('caches', makeMockCacheStorage(mockCache));
    });

    it('saving starts as false', () => {
        const { saving } = useOfflineCache();
        expect(saving.value).toBe(false);
    });

    it('saveResource sets saving to true then false', async () => {
        const { saving, saveResource } = useOfflineCache();
        const promise = saveResource('/resource.pdf');
        // saving is set to true synchronously before await
        expect(saving.value).toBe(true);
        await promise;
        expect(saving.value).toBe(false);
    });

    it('isCached returns false initially for a URL not in cache', async () => {
        mockCache.match.mockResolvedValueOnce(undefined);
        const { isCached } = useOfflineCache();
        const cached = isCached('/not-cached.pdf');
        expect(cached.value).toBe(false);
    });

    it('isCached returns true once the URL is found in cache', async () => {
        mockCache.match.mockResolvedValueOnce(new Response());
        const { isCached } = useOfflineCache();
        const cached = isCached('/cached.pdf');
        // Wait for the async cache lookup to complete
        await new Promise((r) => setTimeout(r, 0));
        expect(cached.value).toBe(true);
    });
});
