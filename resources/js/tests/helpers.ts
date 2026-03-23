import { mount } from '@vue/test-utils';
import PrimeVue from 'primevue/config';
import ToastService from 'primevue/toastservice';
import Aura from '@primevue/themes/aura';

type MountOptions = Parameters<typeof mount>[1];

export function mountWithPrimeVue(
    component: Parameters<typeof mount>[0],
    options: MountOptions = {}
): ReturnType<typeof mount> {
    const { global: globalOpts, ...restOptions } = (options ?? {}) as Record<string, unknown>;
    return mount(component, {
        global: {
            plugins: [[PrimeVue, { theme: { preset: Aura } }], ToastService],
            ...((globalOpts ?? {}) as Record<string, unknown>),
        },
        ...restOptions,
    } as MountOptions);
}
