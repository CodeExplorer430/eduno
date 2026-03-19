import { expect } from 'vitest';
// Import from the dist path to avoid the root-level `export type *` re-export
// that prevents the runtime value from being imported.
import { toHaveNoViolations } from 'vitest-axe/dist/matchers';

expect.extend({ toHaveNoViolations });

declare module 'vitest' {
    // Extend the Assertion interface so TypeScript knows about the custom matcher.
    // eslint-disable-next-line @typescript-eslint/no-explicit-any, @typescript-eslint/no-unused-vars
    interface Assertion<T = any> {
        toHaveNoViolations(): void;
    }
    interface AsymmetricMatchersContaining {
        toHaveNoViolations(): void;
    }
}
