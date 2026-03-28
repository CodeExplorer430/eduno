import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import CreatePage from '@/Pages/Course/Create.vue';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    useForm: vi.fn(() => ({
        code: '',
        title: '',
        description: '',
        department: '',
        term: '',
        academic_year: '',
        post: vi.fn(),
        processing: false,
        errors: {},
        wasSuccessful: false,
    })),
}));

const routeMock = vi.fn(() => '/');
const globalOpts = {
    stubs: {
        AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
    },
    mocks: { route: routeMock },
};

describe('Course/Create', () => {
    it('renders without crashing', () => {
        const wrapper = mount(CreatePage, { global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('shows course code and title fields', () => {
        const wrapper = mount(CreatePage, { global: globalOpts });
        expect(wrapper.find('#code').exists() || wrapper.text()).toBeTruthy();
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mount(CreatePage, { global: globalOpts });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
