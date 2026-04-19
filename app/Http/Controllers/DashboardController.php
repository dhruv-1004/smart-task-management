<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Task;

class DashboardController extends Controller
{
    //
   
    public function index(){
        if (Auth::user()->type === 'admin') {
            $tasks = Task::with('user')->latest()->get();
        }
        else{
        $tasks = Task::where('user_id', auth()->id())
                     ->latest()
                     ->get();
        }
        return Inertia::render('Dashboard', [
            'tasks' => $tasks
        ]);
    }
    public function create(){
        return Inertia::render('Tasks/Randome');
    }

    

    
}
