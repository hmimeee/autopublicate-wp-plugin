<?php

use AP_Route_Service as Route;

Route::get('mi-cuenta', [AP_Profile_Controller::class, 'profile'])->name('profile')->auth();
Route::get('profile', [AP_Profile_Controller::class, 'profile'])->name('profile.main')->auth();
Route::get('profile-edit', [AP_Profile_Controller::class, 'edit'])->name('profile.edit')->auth();
Route::post('profile-edit', [AP_Profile_Controller::class, 'update'])->name('profile.update')->auth();

Route::get('profile/{user}', [AP_Profile_Controller::class, 'index'])->name('user_profile')->auth();
Route::get('contracts', [AP_Contracts_Controller::class, 'index'])->name('contracts.index')->auth();
Route::get('contracts/{user}/create', [AP_Contracts_Controller::class, 'create'])->name('contracts.create')->auth();
Route::post('contracts/{user}/create', [AP_Contracts_Controller::class, 'store'])->name('contracts.store')->auth();
Route::get('contracts/{contract}/view', [AP_Contracts_Controller::class, 'show'])->name('contracts.show')->auth();
Route::post('contracts/{contract}/modify', [AP_Contracts_Controller::class, 'modify'])->name('contracts.modify')->auth();
Route::get('contracts/{contract}/status-update/{status}', [AP_Contracts_Controller::class, 'statusUpdate'])->name('contracts.status-update')->auth();
Route::post('contracts/{contract}/deliver', [AP_Contracts_Controller::class, 'deliver'])->name('contracts.deliver')->auth();
Route::get('contracts/{contract}/delivery-action/{status}', [AP_Contracts_Controller::class, 'deliveryAction'])->name('contracts.delivery-return')->auth();
Route::post('contracts/{contract}/delivery-action/{status}', [AP_Contracts_Controller::class, 'deliveryAction'])->name('contracts.delivery-accept')->auth();
Route::post('contracts/{contract}/comment', [AP_Contracts_Controller::class, 'comment'])->name('contracts.comment')->auth();
