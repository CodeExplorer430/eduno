import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import CoursesIndex from '@/Pages/Student/Courses/Index.vue';

describe('Student/Courses/Index', () => {
    it('renders without crashing', () => {
        const wrapper = mount(CoursesIndex, {
            props: { sections: [] },
            global: {
                stubs: {
                    Head: true,
                    AuthenticatedLayout: { template: '<div><slot /></div>' },
                    CourseCard: true,
                    Link: true,
                },
            },
        });
        expect(wrapper.exists()).toBe(true);
    });

    it('shows empty state when no sections', () => {
        const wrapper = mount(CoursesIndex, {
            props: { sections: [] },
            global: {
                stubs: {
                    Head: true,
                    AuthenticatedLayout: { template: '<div><slot /></div>' },
                    CourseCard: true,
                    Link: true,
                },
            },
        });
        expect(wrapper.text()).toContain('not enrolled');
    });
});
