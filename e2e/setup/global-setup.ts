import { execSync } from 'child_process';

export default function globalSetup(): void {
    execSync(
        'php artisan migrate:fresh --seed --seeder=E2ESeeder --env=e2e --force',
        { stdio: 'inherit' },
    );
}
