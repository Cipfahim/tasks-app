<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Task;
use App\Models\User;
use App\Notifications\NewTaskAssigned;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class TaskController extends Controller
{
    /**
     * Display all tasks.
     */
    public function index()
    {
        return Inertia::render('Tasks/Index', [
            'users' => User::all(),
            'tasks' => Task::with(['creator', 'users'])
                ->latest()
                ->paginate()
        ]);
    }

    /**
     * Display tasks only assigned to current user.
     */
    public function assigned()
    {
        return Inertia::render('Tasks/Index', [
            'users' => User::all(),
            'tasks' => Auth::user()->tasks()
                ->with(['creator', 'users'])
                ->latest()
                ->paginate()
        ]);
    }

    /**
     * Store a newly created task.
     */
    public function store(TaskRequest $request)
    {
        Auth::user()->myTasks()->create($request->validated());

        return Redirect::back()->with('success', 'Task Created');

    }

    /**
     * Update the specified task.
     */
    public function update(TaskRequest $request, Task $task)
    {
        $task->update($request->validated());

        return Redirect::back()->with('success', 'Task Updated');
    }

    /**
     * Remove the specified task.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return Redirect::back()->with('success', 'Task Deleted');
    }

    /**
     * Assign user to a task.
     */
    public function assign(Request $request, Task $task)
    {
        $request->validate([
            'user' => ['required']
        ]);

        $user = User::findOrFail($request->get('user'));

        $task->users()->syncWithoutDetaching($user->id);

        $user->notify(new NewTaskAssigned($task));

        return Redirect::back();
    }

    /**
     * Remove user from a task.
     */
    public function remove(Request $request, Task $task)
    {
        $request->validate([
            'user' => ['required']
        ]);

        $task->users()->detach($request->get('user'));

        return Redirect::back();

    }
}
