import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import EmptyState from '@/Components/EmptyState.vue';

describe('EmptyState', () => {
    it('renders the message prop', () => {
        const wrapper = mount(EmptyState, { props: { message: 'No items found.' } });
        expect(wrapper.text()).toContain('No items found.');
    });

    it('has role="status" for live-region announcements', () => {
        const wrapper = mount(EmptyState, { props: { message: 'No courses yet.' } });
        expect(wrapper.find('[role="status"]').exists()).toBe(true);
    });

    it('has no axe violations', async () => {
        const wrapper = mount(EmptyState, { props: { message: 'Nothing to show.' } });
        expect(
            await axe(wrapper.element, { rules: { region: { enabled: false } } })
        ).toHaveNoViolations();
    });
});
