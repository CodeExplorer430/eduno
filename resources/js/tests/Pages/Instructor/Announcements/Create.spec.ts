import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import CreatePage from '@/Pages/Instructor/Announcements/Create.vue';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    useForm: vi.fn(() => ({
        course_section_id: 1,
        title: '',
        body: '',
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
        Breadcrumb: { template: '<nav />' },
        InputLabel: {
            template: '<label :for="htmlFor">{{ value }}</label>',
            props: ['for', 'value'],
            computed: {
                htmlFor(): string {
                    return (this as unknown as { for: string }).for;
                },
            },
        },
        TextInput: {
            template: '<input :value="modelValue" v-bind="$attrs" />',
            props: ['modelValue'],
            inheritAttrs: false,
        },
        InputError: { template: '<span />' },
    },
    mocks: { route: routeMock },
};

const props = {
    sections: [
        {
            id: 1,
            section_name: 'A',
            course: { id: 1, code: 'CCS123', title: 'HCI' },
        },
    ],
    preselectedSection: null,
};

describe('Instructor/Announcements/Create', () => {
    it('renders without crashing', () => {
        const wrapper = mount(CreatePage, { props, global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('form has title and body fields', () => {
        const wrapper = mount(CreatePage, { props, global: globalOpts });
        expect(wrapper.find('#title').exists()).toBe(true);
        expect(wrapper.find('#body').exists()).toBe(true);
    });

    it('submit button is present', () => {
        const wrapper = mount(CreatePage, { props, global: globalOpts });
        expect(wrapper.find('button[type="submit"]').exists()).toBe(true);
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mount(CreatePage, { props, global: globalOpts });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
