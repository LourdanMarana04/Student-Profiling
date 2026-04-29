<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\StudentActivity;
use App\Models\StudentAffiliation;
use App\Models\StudentSkill;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample students
        $students = [
            [
                'user' => [
                    'name' => 'John Doe',
                    'email' => 'john.doe@student.edu',
                    'password' => Hash::make('password'),
                    'role' => 'student',
                ],
                'student' => [
                    'student_id' => '2024001',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'date_of_birth' => '2000-05-15',
                    'gender' => 'male',
                    'address' => '123 Main St, City, State',
                    'phone' => '+1-555-0123',
                    'year_level' => 3,
                    'status' => 'active',
                ],
                'skills' => ['Programming', 'Basketball'],
                'activities' => [
                    ['activity_name' => 'Programming Club', 'description' => 'Member of the university programming club', 'date' => '2024-01-15'],
                    ['activity_name' => 'Basketball Team', 'description' => 'Varsity basketball player', 'date' => '2024-02-01'],
                ],
                'affiliations' => [
                    ['affiliation_name' => 'Computer Science Society', 'affiliation_type' => 'organization', 'date_joined' => '2024-01-10'],
                    ['affiliation_name' => 'Basketball Team', 'affiliation_type' => 'sport', 'date_joined' => '2024-02-01'],
                ],
            ],
            [
                'user' => [
                    'name' => 'Jane Smith',
                    'email' => 'jane.smith@student.edu',
                    'password' => Hash::make('password'),
                    'role' => 'student',
                ],
                'student' => [
                    'student_id' => '2024002',
                    'first_name' => 'Jane',
                    'last_name' => 'Smith',
                    'date_of_birth' => '2001-08-22',
                    'gender' => 'female',
                    'address' => '456 Oak Ave, City, State',
                    'phone' => '+1-555-0456',
                    'year_level' => 2,
                    'status' => 'active',
                ],
                'skills' => ['Programming', 'Design'],
                'activities' => [
                    ['activity_name' => 'Design Workshop', 'description' => 'Participated in UI/UX design workshop', 'date' => '2024-03-10'],
                ],
                'affiliations' => [
                    ['affiliation_name' => 'Art Club', 'affiliation_type' => 'club', 'date_joined' => '2024-01-20'],
                ],
            ],
        ];

        foreach ($students as $studentData) {
            // Create user
            $user = User::create($studentData['user']);

            // Create student
            $student = Student::create(array_merge($studentData['student'], ['user_id' => $user->id]));

            // Create skills
            foreach ($studentData['skills'] as $skillName) {
                StudentSkill::create([
                    'student_id' => $student->id,
                    'skill_name' => $skillName,
                    'proficiency_level' => 'intermediate',
                ]);
            }

            // Create activities
            foreach ($studentData['activities'] as $activityData) {
                StudentActivity::create(array_merge($activityData, [
                    'student_id' => $student->id,
                    'status' => 'active'
                ]));
            }

            // Create affiliations
            foreach ($studentData['affiliations'] as $affiliationData) {
                StudentAffiliation::create(array_merge($affiliationData, [
                    'student_id' => $student->id,
                    'status' => 'active'
                ]));
            }
        }
    }
}
