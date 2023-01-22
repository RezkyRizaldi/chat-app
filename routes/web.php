<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\GroupChatController;
use App\Http\Controllers\GroupChatConversationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
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

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/chats', ChatController::class)->name('chats.index');
    Route::get('/group-chats', GroupChatController::class)->name('group-chats.index');
    Route::get('/group-conversations', [GroupChatConversationController::class, 'index'])->name('group-conversations.index');
    Route::post('/group-conversations', [GroupChatConversationController::class, 'store'])->name('group-conversations.store');
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('conversations/{receiverId}', [ConversationController::class, 'show'])->name('conversations.show');
    Route::post('conversations/{conversation}/message', [ConversationController::class, 'store'])->name('conversations.store');
});

require __DIR__.'/auth.php';
