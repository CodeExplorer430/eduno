import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import StatCard from '@/Components/StatCard.vue';

const globalOpts = {
    directives: {
        animateonscroll: {},
    },
};

describe('StatCard', () => {
    it('renders without crashing', () => {
        const wrapper = mount(StatCard, {
            props: { label: 'Total Submissions', accent: 'blue' },
            slots: { default: '42' },
            global: globalOpts,
        });
        expect(wrapper.exists()).toBe(true);
    });

    it('shows label text', () => {
        const wrapper = mount(StatCard, {
            props: { label: 'Total Submissions', accent: 'blue' },
            slots: { default: '42' },
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('Total Submissions');
    });

    it('shows slot content', () => {
        const wrapper = mount(StatCard, {
            props: { label: 'Total Submissions', accent: 'blue' },
            slots: { default: '42' },
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('42');
    });

    it('accent bar renders with accent-specific class for blue', () => {
        const wrapper = mount(StatCard, {
            props: { label: 'Total Submissions', accent: 'blue' },
            slots: { default: '42' },
            global: globalOpts,
        });
        const accentBar = wrapper.find('.bg-blue-500');
        expect(accentBar.exists()).toBe(true);
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mount(StatCard, {
            props: { label: 'Total Submissions', accent: 'blue' },
            slots: { default: '42' },
            global: globalOpts,
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
