import { mount } from '@vue/test-utils';
import PrimeVue from 'primevue/config';
import Aura from '@primevue/themes/aura';

type MountOptions = Parameters<typeof mount>[1];

export function mountWithPrimeVue(
    component: Parameters<typeof mount>[0],
    options: MountOptions = {}
): ReturnType<typeof mount> {
    return mount(component, {
        global: {
            plugins: [[PrimeVue, { theme: { preset: Aura } }]],
            ...(options?.global ?? {}),
        },
        ...options,
    });
}
