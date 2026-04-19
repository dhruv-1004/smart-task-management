<?php

namespace App\Services;

use App\Models\Task;
use Carbon\Carbon;

class TaskService
{
    /**
     * Create a new class instance.
     */
    
    public function createTask($data): Task
    {
        $task= new Task();
        $task->title=$data['title'];
        $task->description=$data['description'] ?? null;
        $task->priority=$data['priority'];
        
        $task->due_date = Carbon::now()->addDays($data['due_date'])->toDateString();
        $task->user_id=$data['user_id'];
        $task->save();
        return $task;
    }
    public function updateTask($task,$data){
        if($data->progress==100){
            $task->status='Completed';
        }
        elseif ($data->progress>0){
            $task->status='In-progress';
        }
        $task->title=$data['title'];
        $task->description=$data['description'];
        $task->priority=$data['priority'];

        if(isset($data['due_date'])){
            $task->due_date=now()->addDays($data['due_date'])->toDateString();
        }
        $task->save();
        return $task;
    }

    public function deleteTask($task){
        $task->delete();
    }

    public function getAlltask(){
        return Task::latest()->get();
    }
}
