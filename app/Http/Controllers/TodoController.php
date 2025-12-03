<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function index()
    {
        return response()->json(Todo::all(), 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
        ]);

        $todo = Todo::create($request->all());

        return response()->json($todo, 201);
    }

    public function show($id)
    {
        $todo = Todo::findOrFail($id);

        return response()->json($todo, 200);
    }

    public function update(Request $request, $id)
    {
        $todo = Todo::findOrFail($id);
        $todo->update($request->all());

        return response()->json($todo, 200);
    }

    public function destroy($id)
    {
        $todo = Todo::findOrFail($id);
        $todo->delete();

        return response()->json(['message' => 'Todo deleted successfully'], 200);
    }

    public function toggle($id)
    {
        $todo = Todo::findOrFail($id);

        $todo->completed = !$todo->completed;
        $todo->save();

        return response()->json($todo, 200);
    }
}
