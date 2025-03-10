<?php

use App\Http\Controllers\AttendeesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginRegisterController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CommentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/register', [LoginRegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [LoginRegisterController::class, 'register']);
    Route::get('/login', [LoginRegisterController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginRegisterController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginRegisterController::class, 'logout'])->name('logout');
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware(['auth', 'role:organizer'])->group(function () {
    Route::resource('/events', EventController::class)->only(['index','create','store', 'update', 'destroy']);
    Route::resource('/tickets', TicketController::class);
    Route::get('/attendees', [AttendeesController::class, 'index'])->name('attendees.index');
});

Route::middleware(['auth', 'role:attendee'])->group(function () {
    Route::resource('/events', EventController::class)->only(['show']);
    Route::get('/attendees/search', [AttendeesController::class, 'search']);
    Route::get('/attendees/upcoming', [AttendeesController::class, 'upcoming']);
    Route::post('/comments', [CommentController::class, 'store']);
    Route::get('/comments/{eventId}', [CommentController::class, 'getComments']);
    Route::post('/attendees/checkout', [AttendeesController::class, 'purchaseTickets']);
});