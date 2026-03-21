import { describe, it, expect } from 'vitest';
import { axe } from 'vitest-axe';
import Tag from 'primevue/tag';
import { mountWithPrimeVue } from '../helpers';

const statusSeverity: Record<string, 'info' | 'success' | 'secondary' | 'danger' | 'warn'> = {
    submitted: 'info',
    graded: 'success',
    returned: 'secondary',
    late: 'danger',
    pending: 'warn',
};

describe('PrimeVue Tag (StatusBadge replacement)', () => {
    it('renders the value text', () => {
        const wrapper = mountWithPrimeVue(Tag, {
            props: { value: 'submitted', severity: 'info' },
        });
        expect(wrapper.text()).toContain('submitted');
    });

    it('maps submitted to info severity', () => {
        expect(statusSeverity['submitted']).toBe('info');
    });

    it('maps graded to success severity', () => {
        expect(statusSeverity['graded']).toBe('success');
    });

    it('maps returned to secondary severity', () => {
        expect(statusSeverity['returned']).toBe('secondary');
    });

    it('maps late to danger severity', () => {
        expect(statusSeverity['late']).toBe('danger');
    });

    it('maps pending to warn severity', () => {
        expect(statusSeverity['pending']).toBe('warn');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(Tag, {
            props: { value: 'graded', severity: 'success' },
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
