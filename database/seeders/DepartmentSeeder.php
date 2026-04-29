<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Computer Science',
                'code' => 'CS',
                'description' => 'Department of Computer Science',
                'status' => 'active',
            ],
            [
                'name' => 'Information Technology',
                'code' => 'IT',
                'description' => 'Department of Information Technology',
                'status' => 'active',
            ],
            [
                'name' => 'Information Systems',
                'code' => 'IS',
                'description' => 'Department of Information Systems',
                'status' => 'active',
            ],
            [
                'name' => 'Software Engineering',
                'code' => 'SE',
                'description' => 'Department of Software Engineering',
                'status' => 'active',
            ],
            [
                'name' => 'Mathematics',
                'code' => 'MATH',
                'description' => 'Department of Mathematics',
                'status' => 'active',
            ],
            [
                'name' => 'Physics',
                'code' => 'PHYS',
                'description' => 'Department of Physics',
                'status' => 'active',
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}