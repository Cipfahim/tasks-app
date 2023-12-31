<?php

use App\Http\Controllers\TaskController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::resource('tasks', TaskController::class)->except(['create', 'edit', 'show']);
    Route::get('tasks/assigned', [TaskController::class, 'assigned'])->name('tasks.assigned');
    Route::post('tasks/{task}/assign', [TaskController::class, 'assign'])->name('tasks.assign');
    Route::post('tasks/{task}/remove', [TaskController::class, 'remove'])->name('tasks.remove');
});
