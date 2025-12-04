<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TodoController extends Controller
{
    private function response($status, $message = null, $data = null, $code = 200)
    {
        return response()->json([
            'status'  => $status,
            'message' => $message,
            'data'    => $data
        ], $code);
    }

    public function index(Request $request)
    {
        try {
            $query = Todo::query();

            if ($request->has('completed')) {
                $query->where('completed', $request->boolean('completed'));
            }

            if ($request->filled('search')) {
                $search = $request->input('search');

                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            }

            $todos = $query->orderBy('created_at', 'desc')->get();

            return $this->response('success', 'Lista carregada', $todos);
        } catch (\Exception $e) {
            return $this->response('error', 'Erro ao listar', null, 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title'       => 'required|min:3|max:120',
                'description' => 'nullable|max:255'
            ], [
                'title.required' => 'O título é obrigatório.',
                'title.min'      => 'O título deve ter pelo menos 3 caracteres.',
                'title.max'      => 'O título pode ter no máximo 120 caracteres.',
                'description.max'=> 'A descrição pode ter no máximo 255 caracteres.',
            ]);

            $todo = Todo::create($validated);

            return $this->response('success', 'Todo criado com sucesso', $todo, 201);

        } catch (ValidationException $e) {
            return $this->response('error', 'Falha de validação', $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->response('error', 'Erro ao criar', null, 500);
        }
    }

    public function show($id)
    {
        $todo = Todo::find($id);

        if (!$todo) {
            return $this->response('error', "Todo ID $id não encontrado", null, 404);
        }

        return $this->response('success', 'Registro encontrado', $todo);
    }

    public function update(Request $request, $id)
    {
        $todo = Todo::find($id);

        if (!$todo) {
            return $this->response('error', "Todo ID $id não encontrado", null, 404);
        }

        try {
            $validated = $request->validate([
                'title'       => 'sometimes|min:3|max:120',
                'description' => 'nullable|max:255'
            ]);

            $todo->update($validated);

            return $this->response('success', 'Atualizado com sucesso', $todo);
        } catch (ValidationException $e) {
            return $this->response('error', 'Falha de validação', $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->response('error', 'Erro ao atualizar', null, 500);
        }
    }

    public function destroy($id)
    {
        $todo = Todo::find($id);

        if (!$todo) {
            return $this->response('error', "Todo ID $id não encontrado", null, 404);
        }

        $todo->delete();
        return $this->response('success', 'Todo deleted successfully');
    }

    public function toggle($id)
    {
        $todo = Todo::find($id);

        if (!$todo) {
            return $this->response('error', "Todo ID $id não encontrado", null, 404);
        }

        $todo->completed = !$todo->completed;
        $todo->save();

        return $this->response('success', 'Status alterado', $todo);
    }
}
