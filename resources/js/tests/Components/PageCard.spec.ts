import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import PageCard from '@/Components/PageCard.vue';

describe('PageCard', () => {
    it('renders without crashing', () => {
        const wrapper = mount(PageCard, { slots: { default: 'Content' } });
        expect(wrapper.exists()).toBe(true);
    });

    it('renders as <section> by default', () => {
        const wrapper = mount(PageCard, { slots: { default: 'Content' } });
        expect(wrapper.element.tagName.toLowerCase()).toBe('section');
    });

    it('renders as the element specified by the as prop', () => {
        const wrapper = mount(PageCard, {
            props: { as: 'div' },
            slots: { default: 'Content' },
        });
        expect(wrapper.element.tagName.toLowerCase()).toBe('div');
    });

    it('sets aria-labelledby when headingId is provided', () => {
        const wrapper = mount(PageCard, {
            props: { headingId: 'my-heading' },
            slots: { default: 'Content' },
        });
        expect(wrapper.attributes('aria-labelledby')).toBe('my-heading');
    });

    it('omits aria-labelledby when headingId is not provided', () => {
        const wrapper = mount(PageCard, { slots: { default: 'Content' } });
        expect(wrapper.attributes('aria-labelledby')).toBeUndefined();
    });

    it('renders slot content', () => {
        const wrapper = mount(PageCard, { slots: { default: '<p>Hello</p>' } });
        expect(wrapper.text()).toContain('Hello');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mount(PageCard, {
            props: { headingId: 'heading' },
            slots: { default: '<h2 id="heading">Title</h2><p>Body</p>' },
        });
        const results = await axe(wrapper.element);
        expect(results).toHaveNoViolations();
    });
});
