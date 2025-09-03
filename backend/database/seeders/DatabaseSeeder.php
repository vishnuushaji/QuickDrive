<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        $admin = User::firstOrCreate([
            'email' => 'vishnushaji179@gmail.com',
        ], [
            'name' => 'Super Admin',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
        ]);

        // Create sample client
        $client = User::firstOrCreate([
            'email' => 'client@example.com',
        ], [
            'name' => 'John Client',
            'password' => bcrypt('password'),
            'role' => 'client',
        ]);

        // Create sample developer
        $developer = User::firstOrCreate([
            'email' => 'vishnushaji360@gmail.com',
        ], [
            'name' => 'Jane Developer',
            'password' => bcrypt('password'),
            'role' => 'developer',
        ]);

        // Create multiple projects
        $projects = [
            [
                'name' => 'E-commerce Website',
                'description' => 'Build a modern e-commerce platform with payment integration',
                'start_date' => now(),
                'end_date' => now()->addMonths(4),
                'status' => 'active',
            ],
            [
                'name' => 'Mobile App Development',
                'description' => 'Develop a cross-platform mobile application',
                'start_date' => now()->subDays(10),
                'end_date' => now()->addMonths(3),
                'status' => 'active',
            ],
            [
                'name' => 'CRM System',
                'description' => 'Customer relationship management system',
                'start_date' => now()->subDays(20),
                'end_date' => now()->addMonths(2),
                'status' => 'active',
            ],
            [
                'name' => 'Data Analytics Dashboard',
                'description' => 'Real-time analytics dashboard for business intelligence',
                'start_date' => now()->subDays(30),
                'end_date' => now()->addMonths(5),
                'status' => 'active',
            ],
            [
                'name' => 'API Integration Project',
                'description' => 'Integrate third-party APIs for enhanced functionality',
                'start_date' => now()->subDays(15),
                'end_date' => now()->addMonths(2),
                'status' => 'completed',
            ],
        ];

        foreach ($projects as $projectData) {
            $project = Project::create($projectData);

            // Attach users to project
            $project->users()->attach($client->id, ['role' => 'client']);
            $project->users()->attach($developer->id, ['role' => 'developer']);
        }

        // Get all projects
        $allProjects = Project::all();

        // Create multiple tasks for each project
        $taskTemplates = [
            ['title' => 'Setup Development Environment', 'description' => 'Configure local development environment', 'priority' => 'normal', 'hours' => 8],
            ['title' => 'Database Design', 'description' => 'Design and implement database schema', 'priority' => 'urgent', 'hours' => 16],
            ['title' => 'API Development', 'description' => 'Develop RESTful API endpoints', 'priority' => 'urgent', 'hours' => 24],
            ['title' => 'Frontend Implementation', 'description' => 'Build responsive user interface', 'priority' => 'normal', 'hours' => 32],
            ['title' => 'Testing and QA', 'description' => 'Comprehensive testing and quality assurance', 'priority' => 'normal', 'hours' => 20],
            ['title' => 'Deployment Setup', 'description' => 'Configure production deployment', 'priority' => 'top_urgent', 'hours' => 12],
            ['title' => 'Documentation', 'description' => 'Create technical documentation', 'priority' => 'normal', 'hours' => 16],
            ['title' => 'Code Review', 'description' => 'Peer code review and optimization', 'priority' => 'normal', 'hours' => 8],
        ];

        $statuses = ['pending', 'in_progress', 'completed', 'approved', 'rejected'];

        foreach ($allProjects as $project) {
            $numTasks = rand(3, 8); // Random number of tasks per project
            $selectedTasks = array_rand($taskTemplates, $numTasks);

            if (!is_array($selectedTasks)) {
                $selectedTasks = [$selectedTasks];
            }

            foreach ($selectedTasks as $taskIndex) {
                $taskTemplate = $taskTemplates[$taskIndex];
                $status = $statuses[array_rand($statuses)];

                Task::create([
                    'project_id' => $project->id,
                    'assigned_user_id' => $developer->id,
                    'title' => $taskTemplate['title'],
                    'description' => $taskTemplate['description'],
                    'status' => $status,
                    'priority' => $taskTemplate['priority'],
                    'start_date' => $project->start_date,
                    'due_date' => $status === 'completed' ? $project->start_date->addDays(rand(1, 30)) : $project->end_date,
                    'hours' => $taskTemplate['hours'],
                ]);
            }
        }
    }
}