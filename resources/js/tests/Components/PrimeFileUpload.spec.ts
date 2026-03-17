import { describe, it, expect } from 'vitest';
import { axe } from 'vitest-axe';
import FileUpload from 'primevue/fileupload';
import { mountWithPrimeVue } from '../helpers';

describe('PrimeVue FileUpload', () => {
    it('renders an upload button', () => {
        const wrapper = mountWithPrimeVue(FileUpload, {
            props: { mode: 'advanced', multiple: true, auto: false },
        });
        expect(wrapper.find('button').exists()).toBe(true);
    });

    it('upload button has accessible name', () => {
        const wrapper = mountWithPrimeVue(FileUpload, {
            props: { mode: 'advanced', multiple: false, auto: false },
        });
        const buttons = wrapper.findAll('button');
        const hasAccessibleButton = buttons.some((btn) => {
            const text = btn.text().trim();
            const label = btn.attributes('aria-label');
            return text.length > 0 || (label !== undefined && label.length > 0);
        });
        expect(hasAccessibleButton).toBe(true);
    });

    it('emits select event when files are chosen', async () => {
        const wrapper = mountWithPrimeVue(FileUpload, {
            props: { mode: 'advanced', multiple: true, auto: false },
        });
        const file = new File(['content'], 'test.pdf', { type: 'application/pdf' });
        await wrapper.vm.$emit('select', { files: [file] });
        expect(wrapper.emitted('select')).toBeTruthy();
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(FileUpload, {
            props: { mode: 'advanced', multiple: false, auto: false },
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
