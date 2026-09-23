<?php

namespace Database\Seeders;

use App\Models\AcademicSession;
use App\Models\Attendance;
use App\Models\ClassLevel;
use App\Models\ClassroomAssignment;
use App\Models\FeeStructure;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\ParentProfile;
use App\Models\School;
use App\Models\Section;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\Term;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. School
        $school = School::factory()->create();
        $schoolId = $school->id;

        // 2. SchoolAdmin
        $admin = User::create([
            'name' => 'Demo Admin',
            'email' => 'admin@demoschool.test',
            'password' => Hash::make('demo1234'),
            'school_id' => $schoolId,
        ]);
        $admin->assignRole('SchoolAdmin');

        // 3. Academic session + active term
        $session = AcademicSession::create([
            'school_id' => $schoolId,
            'name' => '2025/2026',
            'start_date' => now()->subMonths(2),
            'end_date' => now()->addMonths(7),
            'is_active' => true,
        ]);

        $term = Term::create([
            'academic_session_id' => $session->id,
            'school_id' => $schoolId,
            'name' => 'First Term',
            'is_active' => true,
        ]);

        // 4. Classes + Sections
        $classNames = ['JSS1', 'JSS2', 'JSS3'];
        $sections = collect();

        foreach ($classNames as $className) {
            $classLevel = ClassLevel::create([
                'school_id' => $schoolId,
                'name' => $className,
            ]);

            foreach (['A', 'B'] as $sectionName) {
                $sections->push(Section::create([
                    'school_id' => $schoolId,
                    'class_level_id' => $classLevel->id,
                    'name' => $sectionName,
                    'capacity' => 40,
                    'is_active' => true,
                ]));
            }
        }

        // 5. Subjects
        $subjectNames = ['Mathematics', 'English Language', 'Basic Science', 'Social Studies', 'Civic Education'];
        $subjects = collect();

        foreach ($subjectNames as $index => $name) {
            $subjects->push(Subject::create([
                'school_id' => $schoolId,
                'name' => $name,
                'code' => 'SUB' . str_pad($index + 1, 2, '0', STR_PAD_LEFT),
            ]));
        }

        // 6. Fee structure for each class
        foreach (ClassLevel::where('school_id', $schoolId)->get() as $classLevel) {
            FeeStructure::create([
                'term_id' => $term->id,
                'class_level_id' => $classLevel->id,
                'name' => 'Tuition Fee',
                'amount' => 25000,
            ]);
        }

        // 7. Teachers (5) with allocations
        $teachers = collect();
        for ($i = 1; $i <= 5; $i++) {
            $teacher = User::create([
                'name' => "Demo Teacher {$i}",
                'email' => "teacher{$i}@demoschool.test",
                'password' => Hash::make('demo1234'),
                'school_id' => $schoolId,
            ]);
            $teacher->assignRole('Teacher');

            TeacherProfile::create([
                'user_id' => $teacher->id,
                'school_id' => $schoolId,
                'employee_id' => TeacherProfile::generateEmployeeId(),
                'qualification' => 'B.Ed',
                'hire_date' => now()->subYear(),
                'phone' => '0800000000' . $i,
                'gender' => $i % 2 === 0 ? 'Female' : 'Male',
            ]);

            $teachers->push($teacher);

            // Assign each teacher to one section + one subject
            ClassroomAssignment::create([
                'teacher_id' => $teacher->id,
                'section_id' => $sections[($i - 1) % $sections->count()]->id,
                'subject_id' => $subjects[($i - 1) % $subjects->count()]->id,
            ]);
        }

        // 8. Students + Parents (6 per section = 36 total)
        foreach ($sections as $section) {
            for ($i = 1; $i <= 6; $i++) {
                $studentUser = User::create([
                    'name' => "Demo Student {$section->name}{$i}",
                    'email' => Str::random(8) . '@demoschool.test',
                    'password' => Hash::make('demo1234'),
                    'school_id' => $schoolId,
                ]);
                $studentUser->assignRole('Student');

                StudentProfile::create([
                    'school_id' => $schoolId,
                    'user_id' => $studentUser->id,
                    'admission_number' => StudentProfile::generateAdmissionNumber(),
                    'class_level_id' => $section->class_level_id,
                    'section_id' => $section->id,
                    'date_of_birth' => now()->subYears(rand(11, 16)),
                    'gender' => $i % 2 === 0 ? 'Female' : 'Male',
                    'address' => 'Demo Address',
                ]);

                // Parent
                $parentUser = User::create([
                    'name' => "Demo Parent {$section->name}{$i}",
                    'email' => Str::random(8) . '@demoschool.test',
                    'password' => Hash::make('demo1234'),
                    'school_id' => $schoolId,
                ]);
                $parentUser->assignRole('Parent');

                ParentProfile::create([
                    'user_id' => $parentUser->id,
                    'occupation' => 'Demo Occupation',
                    'alt_phone' => '0700000000',
                    'address' => 'Demo Address',
                ]);

                $parentUser->children()->attach($studentUser->id, ['relationship' => 'Guardian']);

                // A sample invoice for each student
                $fee = FeeStructure::where('class_level_id', $section->class_level_id)->first();
                if ($fee) {
                    $invoice = Invoice::create([
                        'term_id' => $term->id,
                        'student_id' => $studentUser->id,
                        'invoice_number' => 'INV-' . strtoupper(Str::random(6)),
                        'total_amount' => $fee->amount,
                        'due_date' => now()->addWeeks(2),
                        'status' => 'UNPAID',
                    ]);
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'name' => $fee->name,
                        'amount' => $fee->amount,
                    ]);
                }

                // Sample attendance for the last 5 school days
                for ($d = 0; $d < 5; $d++) {
                    Attendance::create([
                        'term_id' => $term->id,
                        'section_id' => $section->id,
                        'student_id' => $studentUser->id,
                        'date' => now()->subDays($d)->format('Y-m-d'),
                        'status' => 'PRESENT',
                    ]);
                }
            }
        }

        $this->command->info('Demo school seeded: ' . $school->slug);
        $this->command->info('Admin login: admin@demoschool.test / demo1234');
    }
}
