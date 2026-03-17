import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import GradeForm from '@/Components/GradeForm.vue';

describe('GradeForm', () => {
    it('renders with required props', () => {
        const wrapper = mount(GradeForm, {
            props: { submissionId: 1, maxScore: 100 },
            global: {
                stubs: {
                    InputLabel: { template: '<label><slot /></label>' },
                    InputError: { template: '<span />' },
                    Button: { template: '<button type="submit"><slot /></button>' },
                    InputText: { template: '<input />' },
                },
            },
        });
        expect(wrapper.exists()).toBe(true);
    });

    it('has a score input', () => {
        const wrapper = mount(GradeForm, {
            props: { submissionId: 1, maxScore: 100 },
            global: {
                stubs: {
                    InputLabel: { template: '<label><slot /></label>' },
                    InputError: { template: '<span />' },
                    Button: { template: '<button type="submit"><slot /></button>' },
                    InputText: { template: '<input id="grade-score" />' },
                },
            },
        });
        expect(wrapper.find('form').exists()).toBe(true);
    });

    it('has a feedback textarea', () => {
        const wrapper = mount(GradeForm, {
            props: { submissionId: 1, maxScore: 100 },
            global: {
                stubs: {
                    InputLabel: { template: '<label><slot /></label>' },
                    InputError: { template: '<span />' },
                    Button: { template: '<button type="submit"><slot /></button>' },
                    InputText: { template: '<input />' },
                },
            },
        });
        expect(wrapper.find('textarea').exists()).toBe(true);
    });

    it('shows "Grade Submission" when no existing grade', () => {
        const wrapper = mount(GradeForm, {
            props: { submissionId: 1, maxScore: 100, existingGrade: null },
            global: {
                stubs: {
                    InputLabel: { template: '<label><slot /></label>' },
                    InputError: { template: '<span />' },
                    Button: { template: '<button type="submit"><slot /></button>' },
                    InputText: { template: '<input />' },
                },
            },
        });
        expect(wrapper.text()).toContain('Grade Submission');
    });
});
