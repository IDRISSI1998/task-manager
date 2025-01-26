<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::all(); // Fetch all tasks
        return response()->json($tasks);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:tasks,title',
        ]);

        // Create the task
        $task = Task::create([
            'title' => $validated['title'],
            'completed' => false,
        ]);

        return response()->json($task, 201); // Return the created task
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $task = Task::findOrFail($id); // Fetch task or fail if not found
        return response()->json($task);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id); // Fetch task or fail if not found

        // Validate the request
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'completed' => 'sometimes|boolean',
        ]);

        // Update the task
        $task->update($validated);

        return response()->json($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $task = Task::findOrFail($id); // Fetch task or fail if not found
        $task->delete(); // Delete the task

        return response()->json(['message' => 'Task deleted successfully']);
    }
}
