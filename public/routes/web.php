<?php

use Route_Service as Route;

Route::get('profile/{user}', [Profile_Controller::class, 'index'])->name('user_profile');
Route::get('mi-cuenta', [Profile_Controller::class, 'profile'])->name('profile');