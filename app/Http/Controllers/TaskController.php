<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'all');  // Default to 'all' if no filter is selected
    
        $tasksQuery = Task::where('isArchived', 0);
    
        // Apply filter based on task completion status
        if ($filter === 'completed') {
            $tasksQuery->where('isComplete', 1);  // Only completed tasks
        } elseif ($filter === 'pending') {
            $tasksQuery->where('isComplete', 0);  // Only pending tasks
        }
        // If the filter is 'all', no filter is applied, and all tasks will be shown
    
        $tasks = $tasksQuery ->orderByRaw("FIELD(priority_level, 'high', 'medium', 'low')")->get();
    
        return view('tasks.index', compact('tasks'));
    }
    
    

    public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'priority_level' => 'required|in:low,medium,high',
    ]);

    Task::create([
        'title' => $request->title,
        'priority_level' => $request->priority_level,
    ]);

    return redirect('/tasks')->with('success', 'Task added successfully!');
}


    public function update(Request $request, $id)
{
    // Validate input
    $request->validate([
        'title' => 'required|string|max:255',
    ]);

    // Find the task and update its title
    $task = Task::findOrFail($id);
    $task->title = $request->title;
    $task->save();

    return response()->json(['success' => true]); // Return a success response
}



    public function toggleComplete(Task $task)
    {
        $task->update(['completed' => !$task->completed]);
        return back();
    }

    public function destroy(Task $task)
    {
        $task->update(['isArchived' => true]); // Set isArchived to true
        return back(); // Redirect back to the previous page
    }
    public function completeSelected(Request $request)
    {
        $taskIds = $request->input('taskIds');
        
        if (empty($taskIds) || !is_array($taskIds)) {
            return response()->json(['success' => false, 'message' => 'No tasks selected.']);
        }
    
        Task::whereIn('id', $taskIds)->update(['isComplete' => 1]);
    
        return response()->json(['success' => true]);
    }
    
public function deleteSelected(Request $request)
{
    $taskIds = $request->input('taskIds');
    Task::whereIn('id', $taskIds)->update(['isArchived' => 1]);

    return response()->json(['success' => true]);
}
    
}