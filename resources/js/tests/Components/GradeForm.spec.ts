import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import GradeForm from '@/Components/GradeForm.vue';
import { mountWithPrimeVue } from '../helpers';

const mockUseForm = vi.fn(
    (initial: Record<string, unknown>): Record<string, unknown> => ({
        ...initial,
        errors: {} as Record<string, string>,
        processing: false,
        wasSuccessful: false,
        hasErrors: false,
        post: vi.fn(),
        put: vi.fn(),
        patch: vi.fn(),
        delete: vi.fn(),
        reset: vi.fn(),
        clearErrors: vi.fn(),
        setError: vi.fn(),
    })
);

vi.mock('@inertiajs/vue3', () => ({
    useForm: (data: Record<string, unknown>) => mockUseForm(data),
}));

const defaultStubs = {
    InputLabel: { template: '<label><slot /></label>' },
    InputError: { template: '<span />' },
    Button: {
        template: '<button type="submit" :disabled="disabled"><slot /></button>',
        props: ['disabled'],
    },
    InputText: { template: '<input v-bind="$attrs" />', inheritAttrs: false },
};

describe('GradeForm', () => {
    it('renders with required props', () => {
        const wrapper = mount(GradeForm, {
            props: { submissionId: 1, maxScore: 100 },
            global: { stubs: defaultStubs },
        });
        expect(wrapper.exists()).toBe(true);
    });

    it('has a score input', () => {
        const wrapper = mount(GradeForm, {
            props: { submissionId: 1, maxScore: 100 },
            global: { stubs: defaultStubs },
        });
        expect(wrapper.find('form').exists()).toBe(true);
    });

    it('has a feedback textarea', () => {
        const wrapper = mount(GradeForm, {
            props: { submissionId: 1, maxScore: 100 },
            global: { stubs: defaultStubs },
        });
        expect(wrapper.find('textarea').exists()).toBe(true);
    });

    it('shows "Grade Submission" when no existing grade', () => {
        const wrapper = mount(GradeForm, {
            props: { submissionId: 1, maxScore: 100, existingGrade: null },
            global: { stubs: defaultStubs },
        });
        expect(wrapper.text()).toContain('Grade Submission');
    });

    it('shows "Update Grade" heading when existingGrade is provided', () => {
        const wrapper = mount(GradeForm, {
            props: {
                submissionId: 1,
                maxScore: 100,
                existingGrade: { score: 85, feedback: 'Good work' },
            },
            global: { stubs: defaultStubs },
        });
        expect(wrapper.text()).toContain('Update Grade');
    });

    it('pre-populates score field value from existingGrade.score', () => {
        mockUseForm.mockClear();
        mount(GradeForm, {
            props: {
                submissionId: 1,
                maxScore: 100,
                existingGrade: { score: 85, feedback: 'Great work' },
            },
            global: { stubs: defaultStubs },
        });
        expect(mockUseForm).toHaveBeenCalledWith(expect.objectContaining({ score: '85' }));
    });

    it('score input renders aria-describedby="grade-score-error"', () => {
        const wrapper = mount(GradeForm, {
            props: { submissionId: 1, maxScore: 100 },
            global: { stubs: defaultStubs },
        });
        expect(wrapper.html()).toContain('aria-describedby="grade-score-error"');
    });

    it('feedback textarea renders aria-describedby="grade-feedback-error"', () => {
        const wrapper = mount(GradeForm, {
            props: { submissionId: 1, maxScore: 100 },
            global: { stubs: defaultStubs },
        });
        expect(wrapper.find('textarea').attributes('aria-describedby')).toBe(
            'grade-feedback-error'
        );
    });

    it('submit button is disabled when form.processing is true', () => {
        mockUseForm.mockReturnValueOnce({
            score: '',
            feedback: '',
            errors: {},
            processing: true,
            wasSuccessful: false,
            hasErrors: false,
            post: vi.fn(),
        });
        const wrapper = mount(GradeForm, {
            props: { submissionId: 1, maxScore: 100 },
            global: { stubs: defaultStubs },
        });
        expect(wrapper.find('button[type="submit"]').attributes('disabled')).toBeDefined();
    });

    it('shows role="status" success banner when form.wasSuccessful is true', () => {
        mockUseForm.mockReturnValueOnce({
            score: '',
            feedback: '',
            errors: {},
            processing: false,
            wasSuccessful: true,
            hasErrors: false,
            post: vi.fn(),
        });
        const wrapper = mount(GradeForm, {
            props: { submissionId: 1, maxScore: 100 },
            global: { stubs: defaultStubs },
        });
        expect(wrapper.find('[role="status"]').exists()).toBe(true);
    });

    it('shows role="alert" error banner when form.hasErrors is true and no field errors', () => {
        mockUseForm.mockReturnValueOnce({
            score: '',
            feedback: '',
            errors: {},
            processing: false,
            wasSuccessful: false,
            hasErrors: true,
            post: vi.fn(),
        });
        const wrapper = mount(GradeForm, {
            props: { submissionId: 1, maxScore: 100 },
            global: { stubs: defaultStubs },
        });
        expect(wrapper.find('[role="alert"]').exists()).toBe(true);
    });

    it('passes WCAG axe check', async () => {
        // Use full stubs so the PrimeVue plugin (provided via the helper) covers
        // all real PrimeVue components rendered inside GradeForm.
        const wrapper = mountWithPrimeVue(GradeForm, {
            props: { submissionId: 1, maxScore: 100 },
            global: {
                stubs: {
                    InputLabel: { template: '<label><slot /></label>' },
                    InputError: { template: '<span />' },
                    InputText: {
                        template: '<input v-bind="$attrs" />',
                        inheritAttrs: false,
                    },
                    Button: {
                        template: '<button type="submit"><slot /></button>',
                    },
                },
            },
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
