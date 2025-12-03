<?php

use App\Http\Controllers\TodoController;

Route::get('todos', [TodoController::class, 'index']);
Route::post('todos', [TodoController::class, 'store']);
Route::get('todos/{id}', [TodoController::class, 'show']);
Route::put('todos/{id}', [TodoController::class, 'update']);
Route::delete('todos/{id}', [TodoController::class, 'destroy']);
Route::patch('todos/{id}/toggle', [TodoController::class, 'toggle']);
