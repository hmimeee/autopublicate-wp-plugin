<?php

use AP_Route_Service as Route;

Route::get('mi-cuenta', [AP_Profile_Controller::class, 'profile'])->name('profile')->auth();
Route::get('profile', [AP_Profile_Controller::class, 'profile'])->name('profile.main')->auth();
Route::get('profile-edit', [AP_Profile_Controller::class, 'edit'])->name('profile.edit');
Route::post('profile-edit', [AP_Profile_Controller::class, 'update'])->name('profile.update');
Route::get('profile/contracts', [AP_Profile_Controller::class, 'contracts'])->name('profile.contracts');

Route::get('profile/{user}', [AP_Profile_Controller::class, 'index'])->name('user_profile');
Route::get('contracts/{user}/create', [AP_Contracts_Controller::class, 'create'])->name('contracts.create');
Route::post('contracts/{user}/create', [AP_Contracts_Controller::class, 'store'])->name('contracts.store');
Route::get('contracts/{user}/{contract}', [AP_Contracts_Controller::class, 'show'])->name('contracts.show');

Route::get('contracts', [AP_Contracts_Controller::class, 'index'])->name('contracts.index');