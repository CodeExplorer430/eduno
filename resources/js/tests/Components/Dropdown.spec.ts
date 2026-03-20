// @vitest-environment jsdom
// jsdom is used here instead of the default happy-dom because happy-dom has a bug
// where el.style.display cannot be reset to 'none' after it has been set to ''.
// All other component specs use happy-dom (configured globally in vitest.config.ts).

import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { defineComponent } from 'vue';
import { axe } from 'vitest-axe';
import Dropdown from '@/Components/Dropdown.vue';

/** Transparent Transition stub so leave animations do not block v-show DOM updates. */
const TransitionStub = defineComponent({
    render() {
        return this.$slots.default?.();
    },
});

const globalStubs = { Transition: TransitionStub };

const defaultSlots = {
    trigger: '<button type="button">Open menu</button>',
    content: '<a href="#">Item 1</a>',
};

/** v-show sets display:none via inline style when the condition is falsy. */
function isHiddenByVShow(el: HTMLElement): boolean {
    return el.style.display === 'none';
}

describe('Dropdown', () => {
    it('hides the dropdown overlay by default', () => {
        const wrapper = mount(Dropdown, { slots: defaultSlots, global: { stubs: globalStubs } });
        const overlay = wrapper.find('.fixed.inset-0.z-40').element as HTMLElement;
        expect(isHiddenByVShow(overlay)).toBe(true);
    });

    it('shows the overlay when the trigger is clicked', async () => {
        const wrapper = mount(Dropdown, { slots: defaultSlots, global: { stubs: globalStubs } });
        await wrapper.find('button').trigger('click');
        const overlay = wrapper.find('.fixed.inset-0.z-40').element as HTMLElement;
        expect(isHiddenByVShow(overlay)).toBe(false);
    });

    it('closes the dropdown when the trigger is clicked again (toggle)', async () => {
        const wrapper = mount(Dropdown, { slots: defaultSlots, global: { stubs: globalStubs } });
        await wrapper.find('button').trigger('click'); // open
        await wrapper.find('button').trigger('click'); // close
        const overlay = wrapper.find('.fixed.inset-0.z-40').element as HTMLElement;
        expect(isHiddenByVShow(overlay)).toBe(true);
    });

    it('closes the dropdown when Escape is pressed (WCAG 2.1.1)', async () => {
        const wrapper = mount(Dropdown, { slots: defaultSlots, global: { stubs: globalStubs } });
        await wrapper.find('button').trigger('click'); // open

        // Dispatch Escape directly on document (where the component registers its listener)
        document.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape' }));
        await wrapper.vm.$nextTick();

        const overlay = wrapper.find('.fixed.inset-0.z-40').element as HTMLElement;
        expect(isHiddenByVShow(overlay)).toBe(true);
    });

    it('content wrapper has role="menu"', async () => {
        const wrapper = mount(Dropdown, { slots: defaultSlots, global: { stubs: globalStubs } });
        await wrapper.find('button').trigger('click'); // open
        expect(wrapper.find('[role="menu"]').exists()).toBe(true);
    });

    it('exposes open state as slot prop on trigger slot', async () => {
        const wrapper = mount(Dropdown, {
            slots: {
                trigger: `<template #trigger="{ open }"><button type="button" :data-open="open">menu</button></template>`,
                content: '<a href="#">Item</a>',
            },
            global: { stubs: globalStubs },
        });
        // Initially closed
        expect(wrapper.find('button').attributes('data-open')).toBe('false');
        await wrapper.find('button').trigger('click');
        expect(wrapper.find('button').attributes('data-open')).toBe('true');
    });

    it('has no axe violations when closed', async () => {
        const wrapper = mount(Dropdown, { slots: defaultSlots, global: { stubs: globalStubs } });
        expect(
            await axe(wrapper.element, { rules: { region: { enabled: false } } })
        ).toHaveNoViolations();
    });
});
