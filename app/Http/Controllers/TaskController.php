<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Task;
use Illuminate\Http\Request;
use App\Http\Requests\TaskRequest;

class TaskController extends Controller
{
    private $user;

    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index()
    {
        $tasks = $this->user->tasks()
                    ->get(['title', 'description'])
                    ->toArray();

        return $tasks;
    }

    public function store(TaskRequest $request)
    {
        $task = new Task();
        $task->title = $request->title;
        $task->description = $request->description;

        if ($this->user->tasks()->save($task)) {
            return response()->json([
                'success' => true,
                'task' => $task
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'sorry, task cannot be added.'
            ], 500);
        }
    }

    public function show($id)
    {
        $task = $this->user->tasks()->find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'sorry, task with id ' . $id . ' cannot be found.' 
            ], 404);
        }

        return $task;
    }

    public function update(TaskRequest $request, $id)
    {
        $task = $this->user->tasks()->find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'sorry, task with id ' . $id . ' cannot be found.' 
            ], 404);
        }

        $updated = $task->fill($request->all())->save();

        if ($updated) {
            return response()->json([
                'success' => true,
                'message' => 'task updated successfully.'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, task could not be updated.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        $task = $this->user->tasks()->find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'sorry, task with id ' . $id . ' cannot be found.' 
            ], 404);
        }

        if ($task->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'task deleted successfully.'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'task could not be deleted.'
            ], 500);
        }
    }
}
