import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import FileUploadInput from '@/Components/FileUploadInput.vue';

describe('FileUploadInput', () => {
    it('renders without crashing', () => {
        const wrapper = mount(FileUploadInput, {
            props: { modelValue: [] },
        });
        expect(wrapper.exists()).toBe(true);
    });

    it('renders file input element', () => {
        const wrapper = mount(FileUploadInput, {
            props: { modelValue: [] },
        });
        expect(wrapper.find('input[type="file"]').exists()).toBe(true);
    });

    it('has aria-live="polite" region for screen reader announcements', () => {
        const wrapper = mount(FileUploadInput, {
            props: { modelValue: [] },
        });
        expect(wrapper.find('[aria-live="polite"]').exists()).toBe(true);
    });

    it('shows file list when modelValue contains files', () => {
        const mockFile = new File(['content'], 'test-document.pdf', { type: 'application/pdf' });
        const wrapper = mount(FileUploadInput, {
            props: { modelValue: [mockFile] },
        });
        expect(wrapper.text()).toContain('test-document.pdf');
    });

    it('remove button has aria-label containing filename', () => {
        const mockFile = new File(['content'], 'lecture-notes.pdf', { type: 'application/pdf' });
        const wrapper = mount(FileUploadInput, {
            props: { modelValue: [mockFile] },
        });
        const removeButton = wrapper.find('button[aria-label]');
        expect(removeButton.attributes('aria-label')).toContain('lecture-notes.pdf');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mount(FileUploadInput, {
            props: { modelValue: [] },
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
