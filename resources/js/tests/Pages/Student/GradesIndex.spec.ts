import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import GradesIndex from '@/Pages/Student/Grades/Index.vue';

describe('Student/Grades/Index', () => {
    it('renders without crashing', () => {
        const wrapper = mount(GradesIndex, {
            props: { grades: [] },
            global: {
                stubs: {
                    Head: true,
                    AuthenticatedLayout: { template: '<div><slot /></div>' },
                    Link: true,
                },
            },
        });
        expect(wrapper.exists()).toBe(true);
    });
});
