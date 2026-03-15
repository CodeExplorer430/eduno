import { describe, it, expect, beforeEach, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import Modal from '@/Components/Modal.vue';

beforeEach(() => {
    HTMLDialogElement.prototype.showModal = vi.fn();
    HTMLDialogElement.prototype.close = vi.fn();
});

describe('Modal', () => {
    it('renders a <dialog> element', () => {
        const wrapper = mount(Modal);
        expect(wrapper.find('dialog').exists()).toBe(true);
    });

    it('has aria-modal="true" on the dialog element', () => {
        const wrapper = mount(Modal);
        expect(wrapper.find('dialog').attributes('aria-modal')).toBe('true');
    });

    it('emits close when Escape is pressed and show=true and closeable=true', async () => {
        const wrapper = mount(Modal, {
            props: { show: true, closeable: true },
        });

        const event = new KeyboardEvent('keydown', { key: 'Escape', bubbles: true });
        document.dispatchEvent(event);

        await wrapper.vm.$nextTick();
        expect(wrapper.emitted('close')).toBeTruthy();
    });

    it('does not emit close when closeable=false', async () => {
        const wrapper = mount(Modal, {
            props: { show: true, closeable: false },
        });

        const event = new KeyboardEvent('keydown', { key: 'Escape', bubbles: true });
        document.dispatchEvent(event);

        await wrapper.vm.$nextTick();
        expect(wrapper.emitted('close')).toBeFalsy();
    });

    it('does not emit close when show=false', async () => {
        const wrapper = mount(Modal, {
            props: { show: false, closeable: true },
        });

        const event = new KeyboardEvent('keydown', { key: 'Escape', bubbles: true });
        document.dispatchEvent(event);

        await wrapper.vm.$nextTick();
        expect(wrapper.emitted('close')).toBeFalsy();
    });

    it('has no axe violations when closed', async () => {
        const wrapper = mount(Modal, {
            props: { show: false },
            slots: { default: '<p>Modal content</p>' },
        });
        expect(
            await axe(wrapper.element, { rules: { region: { enabled: false } } })
        ).toHaveNoViolations();
    });
});
