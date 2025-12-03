<?php

namespace Tests\Feature;

use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_todos()
    {
        Todo::factory()->count(3)->create();

        $response = $this->getJson('/api/todos');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_can_create_todo()
    {
        $data = [
            'title' => 'Test Todo',
            'description' => 'Test Description',
        ];

        $response = $this->postJson('/api/todos', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment($data);
    }

    public function test_can_show_todo()
    {
        $todo = Todo::factory()->create();

        $response = $this->getJson("/api/todos/{$todo->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'title' => $todo->title,
                     'description' => $todo->description,
                 ]);
    }

    public function test_can_update_todo()
    {
        $todo = Todo::factory()->create();

        $data = [
            'title' => 'Updated Title',
            'completed' => true,
        ];

        $response = $this->putJson("/api/todos/{$todo->id}", $data);

        $response->assertStatus(200)
                 ->assertJsonFragment($data);
    }

    public function test_can_delete_todo()
    {
        $todo = Todo::factory()->create();

        $response = $this->deleteJson("/api/todos/{$todo->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Todo deleted successfully']);

        $this->assertDatabaseMissing('todos', ['id' => $todo->id]);
    }

    public function test_can_toggle_todo_completion()
    {
        $todo = Todo::factory()->create(['completed' => false]);

        $response = $this->patchJson("/api/todos/{$todo->id}/toggle");

        $response->assertStatus(200)
                 ->assertJsonFragment(['completed' => true]);

        $response = $this->patchJson("/api/todos/{$todo->id}/toggle");

        $response->assertStatus(200)
                 ->assertJsonFragment(['completed' => false]);
    }

    public function test_validation_for_creating_todo()
    {
        $response = $this->postJson('/api/todos', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['title']);
    }
}