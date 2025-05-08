<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GuestRegistrationController;
use App\Http\Controllers\api\CityController;
use App\Http\Controllers\GroupController;

Route::get('/', [HomeController::class, 'index'])->name('home');

//route for AJAX request
Route::get('/majors/{educationId}', [GuestRegistrationController::class, 'getMajorsByEducation'])->name('majors.byEducation');

Auth::routes();

// API Routes for city selection - moved outside auth middleware to be accessible
Route::prefix('api')->group(function () {
    Route::get('/cities/search', [App\Http\Controllers\Api\CityController::class, 'search'])->name('api.cities.search');
    Route::post('/cities', [App\Http\Controllers\Api\CityController::class, 'store'])->name('api.cities.store');
});

// Common group routes accessible to both travelers and guides
Route::middleware(['auth'])->group(function () {
    Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
    Route::get('/groups/{group}', [GroupController::class, 'show'])->name('groups.show');
});

/*------------------------------------------
--------------------------------------------
All guest Routes List
--------------------------------------------
--------------------------------------------*/
Route::middleware(['auth', 'user-access:guest'])->group(function () {
    // Disclaimer page - make sure both GET and POST routes are defined
    Route::get('/disclaimerPage', [HomeController::class, 'disclaimerPage'])->name('guest.disclaimer');
    Route::post('/disclaimerPage', [HomeController::class, 'acceptDisclaimer'])->name('guest.disclaimer.accept');

    Route::get('/guest/home', [HomeController::class, 'index'])->name('welcome');
    
    // Guest Registration Routes 
    Route::prefix('registration')->group(function () {
        // Step 1: Basic Info
        Route::get('/basic-info', [GuestRegistrationController::class, 'showBasicInfoForm'])
            ->name('guest.registration.basic-info');
        Route::post('/basic-info', [GuestRegistrationController::class, 'submitBasicInfo'])
            ->name('guest.registration.basic-info.submit');

        // Step 2: Personal Info
        Route::get('/personal-info', [GuestRegistrationController::class, 'showPersonalInfoForm'])
            ->name('guest.registration.personal-info');
        Route::post('/personal-info', [GuestRegistrationController::class, 'submitPersonalInfo'])
            ->name('guest.registration.personal-info.submit');

        // Step 3: Contact Info and Account Creation
        Route::get('/contact-info', [GuestRegistrationController::class, 'showContactInfoForm'])
            ->name('guest.registration.contact-info');
        Route::post('/contact-info', [GuestRegistrationController::class, 'submitContactInfo'])
            ->name('guest.registration.contact-info.submit');
            
        // Step 4: Confirmation page
        Route::get('/confirmation', [GuestRegistrationController::class, 'showConfirmationPage'])
            ->name('guest.registration.confirmation');
        Route::post('/confirmation', [GuestRegistrationController::class, 'submitConfirmation'])
            ->name('guest.registration.confirmation.submit');

    });

    Route::get('/register', function () {
        return redirect()->route('guest.disclaimer');
    })->name('register');
});

/*------------------------------------------
--------------------------------------------
All traveller Routes List
--------------------------------------------
--------------------------------------------*/
Route::middleware(['auth', 'user-access:traveller'])->group(function () {
    Route::get('/traveller/home', [HomeController::class, 'travellerHome'])->name('traveller.home');
    
    // Traveler-specific group actions
    Route::post('/groups/{group}/join', [GroupController::class, 'join'])->name('groups.join');
    Route::delete('/groups/{group}/leave', [GroupController::class, 'leave'])->name('groups.leave');
});

/*------------------------------------------
--------------------------------------------
All Guide Routes List
--------------------------------------------
--------------------------------------------*/
Route::middleware(['auth', 'user-access:guide'])->group(function () {
    

    Route::get('/guide/home', [HomeController::class, 'guideHome'])->name('guide.home');
    
    // Guide-specific group management routes
    Route::get('/guide/groups', [GroupController::class, 'index'])->name('guide.groups.index');
    Route::get('/guide/groups/create', [GroupController::class, 'create'])->name('groups.create');
    Route::post('/guide/groups', [GroupController::class, 'store'])->name('groups.store');
    Route::get('/guide/groups/{group}', [GroupController::class, 'show'])->name('guide.groups.show');
    Route::get('/guide/groups/{group}/edit', [GroupController::class, 'edit'])->name('groups.edit');
    Route::put('/guide/groups/{group}', [GroupController::class, 'update'])->name('groups.update');
    Route::delete('/guide/groups/{group}', [GroupController::class, 'destroy'])->name('groups.destroy');
    Route::post('/guide/groups/{group}/add-member', [GroupController::class, 'addMember'])->name('groups.add-member');
    Route::delete('/guide/groups/{group}/traveller/{traveller}', [GroupController::class, 'removeTraveller'])->name('groups.remove-traveller');
    
    // Add toggle lock route
    Route::post('/guide/groups/{group}/toggle-lock', [GroupController::class, 'toggleLock'])->name('groups.toggle-lock');
});

/*------------------------------------------
--------------------------------------------
All Admin Routes List
--------------------------------------------
--------------------------------------------*/
Route::middleware(['auth', 'user-access:admin'])->group(function () {

    Route::get('/admin/home', [HomeController::class, 'adminHome'])->name('admin.home');
});
