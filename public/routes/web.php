<?php

use AP_Route_Service as Route;

Route::get('profile/{user}', [AP_Profile_Controller::class, 'index'])->name('user_profile');
Route::get('mi-cuenta', [AP_Profile_Controller::class, 'profile'])->name('profile');