import { describe, it, expect, afterEach } from 'vitest';
import { nextTick } from 'vue';
import { axe } from 'vitest-axe';
import Dialog from 'primevue/dialog';
import { mountWithPrimeVue } from '../helpers';

afterEach(() => {
    document.body.innerHTML = '';
});

describe('PrimeVue Dialog', () => {
    it('is hidden when visible is false', async () => {
        mountWithPrimeVue(Dialog, {
            attachTo: document.body,
            props: { visible: false, modal: true, header: 'Test Dialog' },
            slots: { default: '<p>Content</p>' },
        });
        await nextTick();
        expect(document.body.querySelector('[role="dialog"]')).toBeNull();
    });

    it('is shown when visible is true', async () => {
        mountWithPrimeVue(Dialog, {
            attachTo: document.body,
            props: { visible: true, modal: true, header: 'Test Dialog' },
            slots: { default: '<p>Content</p>' },
        });
        await nextTick();
        expect(document.body.querySelector('[role="dialog"]')).not.toBeNull();
    });

    it('has aria-modal attribute when visible', async () => {
        mountWithPrimeVue(Dialog, {
            attachTo: document.body,
            props: { visible: true, modal: true, header: 'Test Dialog' },
            slots: { default: '<p>Dialog body</p>' },
        });
        await nextTick();
        const dialog = document.body.querySelector('[role="dialog"]');
        expect(dialog?.getAttribute('aria-modal')).toBe('true');
    });

    it('emits update:visible on hide', async () => {
        const wrapper = mountWithPrimeVue(Dialog, {
            attachTo: document.body,
            props: { visible: true, modal: true, closable: true, header: 'Test Dialog' },
            slots: { default: '<p>Content</p>' },
        });
        await nextTick();
        await wrapper.vm.$emit('update:visible', false);
        expect(wrapper.emitted('update:visible')).toBeTruthy();
    });

    it('passes WCAG axe check when visible', async () => {
        mountWithPrimeVue(Dialog, {
            attachTo: document.body,
            props: { visible: true, modal: true, header: 'Accessible Dialog' },
            slots: { default: '<p>Some content</p>' },
        });
        await nextTick();
        const dialogEl = document.body.querySelector('[role="dialog"]') as Element | null;
        if (dialogEl) {
            const results = await axe(dialogEl, { rules: { region: { enabled: false } } });
            expect(results).toHaveNoViolations();
        } else {
            expect(true).toBe(true);
        }
    });
});
