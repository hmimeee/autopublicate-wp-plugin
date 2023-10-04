<?php

use Route_Service as Route;

Route::get('profile/{user}', [Profile_Controller::class, 'index']);
Route::get('mi-cuenta', [Profile_Controller::class, 'profile']);