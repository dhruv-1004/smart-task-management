<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;
class AdminController extends Controller
{
    //
    public function index(Request $request){
        $query=Task::query();
        if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('title', 'like', '%' . $request->search . '%')
              ->orWhere('description', 'like', '%' . $request->search . '%')
              ->orWhere('status','Pending')
              ->orWhere('status','Completed');
        });

    }
    if($request->filled('Pending')){
        $query->where('status',$request->status);
    }

    $tasks = $query->get();
    return Inertia::render('Tasks/Index', [
        'tasks' => $tasks,
        'filters' => $request->only(['search','Pending']) // also renamed for consistency
    ]);

    }
}
