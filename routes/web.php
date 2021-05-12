<?php

use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// User Profile
Route::get('profile', [App\Http\Controllers\UserProfileController::class, 'show'])->middleware('auth')->name('profile.show');
Route::post('profile', [App\Http\Controllers\UserProfileController::class, 'update'])->middleware('auth')->name('profile.update');

/**
 * Override the default auth register route to add middleware.
 */
Route::get('register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register')->middleware('hasInvitation');
Route::get('register/request', [App\Http\Controllers\Auth\RegisterController::class, 'requestInvitation'])->name('requestInvitation');

/**
 * Invitations group with auth middleware.
 * Eventhough we only have one route currently, the route group is for future updates.
 */
Route::group([
    'middleware' => ['auth', 'admin'],
    'prefix' => 'invitations'
], function() {
    Route::get('', [App\Http\Controllers\InvitationsController::class, 'index'])->name('showInvitations');
});

/**
 * Route for storing the invitation. Only for guest users.
 */
Route::post('invitations', [App\Http\Controllers\InvitationsController::class, 'store'])->middleware('guest')->name('storeInvitation');

// Verify email
Route::get('/email/verify/{id}/{hash}', [App\Http\Controllers\VerifyEmailController::class, '__invoke'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

// Resend link to verify email
Route::post('/email/verify/resend', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth:api', 'throttle:6,1'])->name('verification.send');