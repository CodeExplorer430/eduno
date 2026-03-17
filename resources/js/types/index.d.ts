import { Config } from 'ziggy-js';
import type { UserPreferences } from '@/Types/models';

export interface User {
    id: number;
    name: string;
    email: string;
    role: 'student' | 'instructor' | 'admin';
    email_verified_at?: string;
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: {
        user: User;
        preferences?: UserPreferences | null;
    };
    ziggy: Config & { location: string };
    flash?: Record<string, string>;
    features: {
        'high-contrast': boolean;
        'simplified-layout': boolean;
    };
};
