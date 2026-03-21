import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import CourseCard from '@/Components/CourseCard.vue';

const stubs = {
    Link: {
        template: '<a :href="$attrs.href ?? \'#\'" v-bind="$attrs"><slot /></a>',
        inheritAttrs: false,
    },
};

const globalOpts = {
    stubs,
    mocks: { route: vi.fn(() => '/') },
};

const baseSection = {
    id: 10,
    section_name: 'A',
    schedule_text: 'MWF 10:00–11:30',
    course: { id: 1, code: 'CCS101', title: 'Intro to Computing', status: 'published' },
    instructor: { id: 2, name: 'Dr. Santos' },
};

describe('CourseCard', () => {
    it('renders course.code and course.title', () => {
        const wrapper = mount(CourseCard, {
            props: { section: baseSection },
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('CCS101');
        expect(wrapper.text()).toContain('Intro to Computing');
    });

    it('link aria-label contains both the course code and title', () => {
        const wrapper = mount(CourseCard, {
            props: { section: baseSection },
            global: globalOpts,
        });
        const link = wrapper.find('a[aria-label]');
        expect(link.attributes('aria-label')).toContain('CCS101');
        expect(link.attributes('aria-label')).toContain('Intro to Computing');
    });

    it('section_name appears in a dd', () => {
        const wrapper = mount(CourseCard, {
            props: { section: baseSection },
            global: globalOpts,
        });
        const dds = wrapper.findAll('dd');
        const sectionDD = dds.find((dd) => dd.text().includes('A'));
        expect(sectionDD).toBeDefined();
    });

    it('renders schedule_text when provided', () => {
        const wrapper = mount(CourseCard, {
            props: { section: baseSection },
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('MWF 10:00–11:30');
    });

    it('does not render the schedule section when schedule_text is null', () => {
        const wrapper = mount(CourseCard, {
            props: { section: { ...baseSection, schedule_text: null } },
            global: globalOpts,
        });
        expect(wrapper.text()).not.toContain('Schedule');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mount(CourseCard, {
            props: { section: baseSection },
            global: globalOpts,
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
