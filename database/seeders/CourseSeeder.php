<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            // Computer Science Courses
            [
                'course_code' => 'CS101',
                'course_name' => 'Introduction to Computer Science',
                'description' => 'Fundamental concepts of computer science and programming',
                'credits' => 3,
                'department_id' => 1, // Computer Science
                'status' => 'active',
            ],
            [
                'course_code' => 'CS201',
                'course_name' => 'Data Structures and Algorithms',
                'description' => 'Advanced data structures and algorithm design',
                'credits' => 3,
                'department_id' => 1,
                'status' => 'active',
            ],
            [
                'course_code' => 'CS301',
                'course_name' => 'Database Systems',
                'description' => 'Database design, SQL, and database management systems',
                'credits' => 3,
                'department_id' => 1,
                'status' => 'active',
            ],
            [
                'course_code' => 'CS401',
                'course_name' => 'Software Engineering',
                'description' => 'Software development methodologies and project management',
                'credits' => 3,
                'department_id' => 1,
                'status' => 'active',
            ],

            // Information Technology Courses
            [
                'course_code' => 'IT101',
                'course_name' => 'Information Technology Fundamentals',
                'description' => 'Basic concepts of information technology and systems',
                'credits' => 3,
                'department_id' => 2, // Information Technology
                'status' => 'active',
            ],
            [
                'course_code' => 'IT201',
                'course_name' => 'Network Administration',
                'description' => 'Network setup, configuration, and management',
                'credits' => 3,
                'department_id' => 2,
                'status' => 'active',
            ],
            [
                'course_code' => 'IT301',
                'course_name' => 'Cybersecurity',
                'description' => 'Information security principles and practices',
                'credits' => 3,
                'department_id' => 2,
                'status' => 'active',
            ],
            [
                'course_code' => 'IT401',
                'course_name' => 'Web Development',
                'description' => 'Modern web development technologies and frameworks',
                'credits' => 3,
                'department_id' => 2,
                'status' => 'active',
            ],

            // Information Systems Courses
            [
                'course_code' => 'IS201',
                'course_name' => 'Systems Analysis and Design',
                'description' => 'Analysis and design of information systems',
                'credits' => 3,
                'department_id' => 3, // Information Systems
                'status' => 'active',
            ],
            [
                'course_code' => 'IS301',
                'course_name' => 'Enterprise Systems',
                'description' => 'Enterprise resource planning and management systems',
                'credits' => 3,
                'department_id' => 3,
                'status' => 'active',
            ],

            // Software Engineering Courses
            [
                'course_code' => 'SE201',
                'course_name' => 'Software Requirements Engineering',
                'description' => 'Requirements gathering, analysis, and specification',
                'credits' => 3,
                'department_id' => 4, // Software Engineering
                'status' => 'active',
            ],
            [
                'course_code' => 'SE301',
                'course_name' => 'Software Testing and Quality Assurance',
                'description' => 'Testing methodologies and quality assurance practices',
                'credits' => 3,
                'department_id' => 4,
                'status' => 'active',
            ],

            // Mathematics Courses
            [
                'course_code' => 'MATH101',
                'course_name' => 'Discrete Mathematics',
                'description' => 'Mathematical foundations for computer science',
                'credits' => 3,
                'department_id' => 5, // Mathematics
                'status' => 'active',
            ],
            [
                'course_code' => 'MATH201',
                'course_name' => 'Linear Algebra',
                'description' => 'Matrix operations and linear transformations',
                'credits' => 3,
                'department_id' => 5,
                'status' => 'active',
            ],

            // Physics Courses
            [
                'course_code' => 'PHYS101',
                'course_name' => 'Physics for Computer Science',
                'description' => 'Physics concepts relevant to computing',
                'credits' => 3,
                'department_id' => 6, // Physics
                'status' => 'active',
            ],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}