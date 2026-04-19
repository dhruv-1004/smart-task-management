<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Services\TaskService;
use App\Http\Requests\TaskRequest;
use App\Models\User;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request){
        if (Auth::user()->type === 'admin') {
            $query = Task::with('user')->latest();

            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('title', 'like', '%' . $request->search . '%')
                        ->orWhere('description', 'like', '%' . $request->search . '%');
                        //->orWhere('status','Pending')
                        
                });
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $tasks = $query->get();
            $user=Auth::user()->latest()->get();
            return Inertia::render('Tasks/Index', [
                'tasks' => $tasks,
                'filters' => $request->only(['search', 'status']), // also renamed for consistency
                'isAdmin' => Auth::user()->type,
                'user'=> $user
            ]);
        } else {
           $query = Auth::user()->tasks()->latest();

            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('title', 'like', '%' . $request->search . '%')
                        ->orWhere('description', 'like', '%' . $request->search . '%');
                        //->orWhere('status','Pending')
                        
                });
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $tasks = $query->get();
            // ✅ THIS was missing
            
            return Inertia::render('Tasks/Index', [
                'tasks' => $tasks,
                'filters' => $request->only(['search', 'status']), // also renamed for consistency
                'isAdmin' => Auth::user()->type,
               
            ]);
        }
    }

    //if(User::where('type','admin')){
    //    $query = Task::query();
    //    $type= User::query();
    //    $type->where('type','admin');
    //    if ($request->filled('search')) {
    //    $query->where(function ($q) use ($request) {
    //        $q->where('title', 'like', '%' . $request->search . '%')
    //          ->orWhere('description', 'like', '%' . $request->search . '%')
    //          ->orWhere('status','Pending')
    //          ->orWhere('status','Completed');
    //    });
    //
    //}
    //if($request->filled('Pending')){
    //    $query->where('status',$request->status);
    //}
    //
    //$tasks = $query->get(); // ✅ THIS was missing
    //$user=$type->get();
    //return Inertia::render('Tasks/Randome', [
    //    'tasks' => $tasks,
    //    'filters' => $request->only(['search','Pending']), // also renamed for consistency
    //    'type'=>$user
    //]);
    //}

    public function show(Request $request){
        $query = Task::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%')
                    ->orWhere('status', 'Pending')
                    ->orWhere('status', 'Completed');
            });
        }
        if ($request->filled('Pending')) {
            $query->where('status', $request->status);
        }

        $tasks = $query->get(); // ✅ THIS was missing

        return Inertia::render('Tasks/Randome', [
            'tasks' => $tasks,
            'filters' => $request->only(['search', 'Pending']) // also renamed for consistency
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){
        return Inertia::render('Tasks/CreateTask');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request)
    {   if(Auth::user()->type==='admin'){
        $data = $request->validated();
        $this->taskService->createTask($data);
        return redirect()->route('tasks.index')->with('Success', 'Task Created');
        }
        
        return abort(403,'Not Allowed To do...');
    }

    /**
     * Display the specified resource.
     */
    //public function show(Task $task)
    //{
    //    
    //}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task, TaskRequest $request)
    {
        $this->authorizeTaskOwnership($task);
        $this->taskService->updateTask($task, $request->validated());

        return redirect()->route('tasks.index')->with('Success', 'Task Edited Successfully');
    }
    public function updateProgress(Request $request, Task $task){
        $request->validate([
            'progress'=>'required|integer|min:0|max:100'
        ]);
        $status = match (true) {
        $request->progress == 0 => 'Pending',
        $request->progress == 100 => 'Completed',
        default => 'In-progress',
    };

        $task->update([
            'progress'=>$request->progress,
            'status'=>$status
        ]);
        return redirect()->route('tasks.index')->with('Success', 'Progress Edited Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->authorizeTaskOwnership($task);

        $this->taskService->deleteTask($task);

        return redirect()->route('tasks.index')->with('Success', 'Task Deleted Successfully');
    }

    public function complete(Task $task)
    {
        $this->authorizeTaskOwnership($task);

        $task->update(['status' => 'completed']);

        return redirect()->route('tasks.index')->with('Success', 'Task Completed Successfully');
    }

    private function authorizeTaskOwnership(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
    }
}
