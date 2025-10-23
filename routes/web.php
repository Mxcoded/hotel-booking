<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Public Frontend Controllers
use App\Http\Controllers\PageController;
use App\Http\Controllers\LeadController;

// Admin Panel Controllers
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\WhatsappLeadController;
use App\Http\Controllers\FeedbackController as AdminFeedbackController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\Admin\AttractionController;


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

// Public Frontend Routes
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/rooms', [PageController::class, 'rooms'])->name('rooms');
Route::get('/gallery', [PageController::class, 'gallery'])->name('gallery');
Route::get('/rooms/{room}', [PageController::class, 'showRoom'])->name('rooms.show');
Route::post('/contact', [PageController::class, 'storeContact'])->name('contact.store');
Route::get('/local-guide', [PageController::class, 'localGuide'])->name('local-guide');
// New Routes for Favorites
Route::get('/favorites', [PageController::class, 'favorites'])->name('favorites');
Route::post('/api/get-favorite-rooms', [PageController::class, 'getFavoriteRooms'])->name('api.favorites');

// WhatsApp Lead Capture Route
Route::post('/log-whatsapp-lead', [LeadController::class, 'store'])->name('whatsapp.lead.store');
Route::get('/feedback', [FeedbackController::class, 'create'])->name('feedback.create');
Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');


// Authentication Routes (Login, Register, etc.)
Auth::routes(['register' => false]);

// Admin Panel Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Room Management (with media routes)
    Route::post('rooms/{room}/media', [RoomController::class, 'storeMedia'])->name('rooms.media.store');
    Route::delete('rooms/media/{media}', [RoomController::class, 'destroyMedia'])->name('rooms.media.destroy');
    Route::resource('rooms', RoomController::class);

    // Gallery Management
    Route::get('gallery', [GalleryController::class, 'index'])->name('gallery.index');
    Route::post('gallery', [GalleryController::class, 'store'])->name('gallery.store');
    Route::delete('gallery/{gallery}', [GalleryController::class, 'destroy'])->name('gallery.destroy');

    // Settings Management
    Route::resource('settings', SettingController::class);
    Route::resource('attractions', AttractionController::class)->except('show');


    // Contact Messages Management
    Route::get('contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::get('contacts/{contact}', [ContactController::class, 'show'])->name('contacts.show');
    Route::put('contacts/{contact}', [ContactController::class, 'update'])->name('contacts.update');
    Route::delete('contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');

    // WhatsApp Leads Management
    Route::get('whatsapp-leads', [WhatsappLeadController::class, 'index'])->name('whatsapp-leads.index');
    Route::delete('whatsapp-leads/{whatsappLead}', [WhatsappLeadController::class, 'destroy'])->name('whatsapp-leads.destroy');

    // Guest Feedback

    Route::get('/feedback', [AdminFeedbackController::class, 'index'])->name('feedback.index');
    Route::get('/feedback/{feedback}', [AdminFeedbackController::class, 'show'])->name('feedback.show');
    Route::patch('/feedback/{feedback}/toggle-approval', [AdminFeedbackController::class, 'toggleApproval'])->name('feedback.toggleApproval');
    Route::delete('/feedback/{feedback}', [AdminFeedbackController::class, 'destroy'])->name('feedback.destroy');
});
