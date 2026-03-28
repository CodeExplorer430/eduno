import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import EditPage from '@/Pages/Instructor/Announcements/Edit.vue';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    useForm: vi.fn((initial: Record<string, unknown>) => ({
        ...initial,
        patch: vi.fn(),
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
    announcement: {
        id: 1,
        title: 'Hello World',
        body: 'Announcement body text.',
        course_section_id: 1,
        published_at: null,
    },
    sections: [
        {
            id: 1,
            section_name: 'A',
            course: { id: 1, code: 'CCS123', title: 'HCI' },
        },
    ],
};

describe('Instructor/Announcements/Edit', () => {
    it('renders without crashing', () => {
        const wrapper = mount(EditPage, { props, global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('pre-fills title field with announcement.title', () => {
        const wrapper = mount(EditPage, { props, global: globalOpts });
        const titleInput = wrapper.find('#title');
        expect((titleInput.element as HTMLInputElement).value).toBe('Hello World');
    });

    it('form has body field', () => {
        const wrapper = mount(EditPage, { props, global: globalOpts });
        expect(wrapper.find('#body').exists()).toBe(true);
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mount(EditPage, { props, global: globalOpts });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
