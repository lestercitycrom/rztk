<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;


Route::get('/cron/rozetka', function(Request $request) {
    /*
	if ($request->query('key') !== env('CRON_KEY')) {
        abort(403);
    }
	*/

    Artisan::call('rozetka:parse');
    return response('OK', 200);
});
