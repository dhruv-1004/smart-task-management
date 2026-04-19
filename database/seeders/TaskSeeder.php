<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Task;
class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Task::create([
            'title' => 'Project Pages',
        'description' => 'Make Landing page and Login/Logout page',
        'status' => 'Pending',
        'priority' => 'High',
        'due_date' => now()->addDays(31-03-2026),
        ]);
    }
}
