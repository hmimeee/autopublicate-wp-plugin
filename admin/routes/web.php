<?php

use AP_Route_Service as Route;

Route::get('autopublicate', [AP_Dashboard_Controller::class, 'index']);
Route::get('settings', [AP_Settings_Controller::class, 'index']);
Route::post('settings', [AP_Settings_Controller::class, 'update']);