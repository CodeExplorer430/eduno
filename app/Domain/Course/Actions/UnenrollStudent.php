<?php

declare(strict_types=1);

namespace App\Domain\Course\Actions;

use App\Domain\Course\Models\Enrollment;
use Illuminate\Support\Facades\DB;

class UnenrollStudent
{
    public function handle(Enrollment $enrollment): void
    {
        $enrollment->update(['status' => 'withdrawn']);

        DB::table('audit_logs')->insert([
            'actor_id' => $enrollment->user_id,
            'action' => 'enrollment.deleted',
            'entity_type' => Enrollment::class,
            'entity_id' => $enrollment->id,
            'metadata' => json_encode([
                'student_id' => $enrollment->user_id,
                'course_section_id' => $enrollment->course_section_id,
            ]),
            'created_at' => now(),
        ]);
    }
}
