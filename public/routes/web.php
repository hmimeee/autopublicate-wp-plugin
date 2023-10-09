<?php

use AP_Route_Service as Route;

Route::get('mi-cuenta', [AP_Profile_Controller::class, 'profile'])->name('profile')->auth();
Route::get('profile', [AP_Profile_Controller::class, 'profile'])->name('profile.main')->auth();
Route::get('profile/{user}', [AP_Profile_Controller::class, 'index'])->name('user_profile');
Route::get('profile-edit', [AP_Profile_Controller::class, 'edit'])->name('profile.edit');
Route::post('profile-edit', [AP_Profile_Controller::class, 'update'])->name('profile.update');