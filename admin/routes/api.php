<?php
use AP_Route_Service as Route;

Route::api('payout_request_view', [AP_Payout_Controller::class, 'show']);