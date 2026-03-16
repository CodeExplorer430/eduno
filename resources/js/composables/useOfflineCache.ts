import { ref, type Ref } from 'vue';

const CACHE_NAME = 'eduno-resources';

export function useOfflineCache(): {
    saving: Ref<boolean>;
    isCached: (url: string) => Ref<boolean>;
    saveResource: (url: string) => Promise<void>;
} {
    const saving = ref(false);

    function isCached(url: string): Ref<boolean> {
        const cached = ref(false);

        if (!('caches' in window)) return cached;

        caches.open(CACHE_NAME).then((cache) => {
            cache.match(url).then((response) => {
                cached.value = response !== undefined;
            });
        });

        return cached;
    }

    async function saveResource(url: string): Promise<void> {
        if (!('caches' in window)) return;

        saving.value = true;
        try {
            const cache = await caches.open(CACHE_NAME);
            await cache.add(url);
        } finally {
            saving.value = false;
        }
    }

    return { saving, isCached, saveResource };
}
