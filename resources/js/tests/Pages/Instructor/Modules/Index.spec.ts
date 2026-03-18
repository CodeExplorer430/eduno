import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import IndexPage from '@/Pages/Instructor/Modules/Index.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    Link: { template: '<a v-bind="$attrs"><slot /></a>', inheritAttrs: false },
    router: { delete: vi.fn() },
}));

const stubs = {
    AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
    Head: true,
    Link: { template: '<a v-bind="$attrs"><slot /></a>', inheritAttrs: false },
    Dialog: {
        template: '<div v-if="$attrs.visible"><slot /><slot name="footer" /></div>',
        inheritAttrs: false,
    },
    Button: { template: '<button><slot /></button>' },
};

const section = {
    id: 1,
    section_name: 'A',
    course: { id: 1, code: 'CCS123', title: 'Intro to HCI' },
    modules: [] as (typeof moduleFixture)[],
};

const moduleFixture = {
    id: 10,
    title: 'Week 1',
    description: null,
    order_no: 1,
    published_at: null,
    lessons: [],
};

const routeMock = vi.fn(() => '/');

const globalOpts = { stubs, mocks: { route: routeMock } };

describe('Instructor/Modules/Index', () => {
    it('renders without crashing', () => {
        const wrapper = mount(IndexPage, { global: globalOpts, props: { section } });
        expect(wrapper.exists()).toBe(true);
    });

    it('shows "No modules yet." text when modules is empty', () => {
        const wrapper = mount(IndexPage, { global: globalOpts, props: { section } });
        expect(wrapper.html()).toContain('No modules yet.');
    });

    it('"Add Module" link is present', () => {
        const wrapper = mount(IndexPage, { global: globalOpts, props: { section } });
        expect(wrapper.html()).toContain('Add Module');
    });

    it('module article has aria-labelledby="module-title-10" when modules exist', () => {
        const sectionWithModules = { ...section, modules: [moduleFixture] };
        const wrapper = mount(IndexPage, {
            global: globalOpts,
            props: { section: sectionWithModules },
        });
        expect(wrapper.html()).toContain('aria-labelledby="module-title-10"');
    });

    it('"Add lesson" link has aria-label="Add lesson to Week 1"', () => {
        const sectionWithModules = { ...section, modules: [moduleFixture] };
        const wrapper = mount(IndexPage, {
            global: globalOpts,
            props: { section: sectionWithModules },
        });
        expect(wrapper.html()).toContain('aria-label="Add lesson to Week 1"');
    });

    it('"Edit module" link has aria-label="Edit module Week 1"', () => {
        const sectionWithModules = { ...section, modules: [moduleFixture] };
        const wrapper = mount(IndexPage, {
            global: globalOpts,
            props: { section: sectionWithModules },
        });
        expect(wrapper.html()).toContain('aria-label="Edit module Week 1"');
    });

    it('delete confirmation dialog is not visible initially', () => {
        const sectionWithModules = { ...section, modules: [moduleFixture] };
        const wrapper = mount(IndexPage, {
            global: globalOpts,
            props: { section: sectionWithModules },
        });
        const vm = wrapper.vm as unknown as { confirmDialog: { visible: boolean } };
        expect(vm.confirmDialog.visible).toBe(false);
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(IndexPage, {
            props: { section },
            global: {
                mocks: { route: routeMock },
                stubs,
            },
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
