<?php

use AP_Route_Service as Route;

Route::get('dashboard', [AP_Dashboard_Controller::class, 'index']);

Route::get('contracts', [AP_Contract_Controller::class, 'index']);
Route::get('contract_view', [AP_Contract_Controller::class, 'show']);
Route::post('contract_resolution', [AP_Contract_Controller::class, 'resolution']);

Route::get('settings', [AP_Settings_Controller::class, 'index']);
Route::post('settings', [AP_Settings_Controller::class, 'update']);