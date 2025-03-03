<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Log;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sortBy = $request->get('sort_by', 'due_date');
        $sortDirection = $request->get('sort_direction', 'desc');

        $validSorts = ['due_date', 'title'];
        $validDirections = ['asc', 'desc'];

        if (!in_array($sortBy, $validSorts)) {
            $sortBy = 'due_date';
        }

        if (!in_array($sortDirection, $validDirections)) {
            $sortDirection = 'desc';
        }

        $tasks = Task::orderBy($sortBy, $sortDirection)->paginate(5);

        return view('tasks.index', [
            'tasks' => $tasks,
            'sortBy' => $sortBy,
            'sortDirection' => $sortDirection
        ]);
    }
    public function create()
    {
        return view('tasks.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'due_date' => 'required|date',
        ]);

        $task = Task::create($validated);
        Log::info("Task created: {$task->id}");
        return redirect()->route('tasks.index')->with('success', 'Task created!');
    }

    /**
     * Display the specified resource.
     */
    public function edit(Task $task)
    {
        Log::debug("Editing task ID: {$task->id}");
        return view('tasks.edit', compact('task'));
    }

    // Обновление задачи
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'due_date' => 'required|date',
            'status' => 'required|in:' . implode(',', array_keys(Task::getStatuses()))
        ]);

        $task->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully'
        ]);
    }

    // Удаление задачи
    public function destroy(Task $task)
    {
        try {
            $task->delete();
            Log::info("Task deleted: {$task->id}");
            return redirect()->route('tasks.index')->with('success', 'Task deleted!');
        } catch (\Exception $e) {
            Log::error("Error deleting task: " . $e->getMessage());
            return back()->with('error', 'Error deleting task!');
        }
    }
}