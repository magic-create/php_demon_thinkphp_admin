<?php

use think\facade\Route;

/*
|--------------------------------------------------------------------------
| Admin Custom Route
|--------------------------------------------------------------------------
|
| Use $router to define the route, and then use $run to run it
|
*/

//  Welcome
Route::any('/welcome', function() { return response('welcome'); });
