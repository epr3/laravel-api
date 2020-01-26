<?php

Route::group([
    'middleware' => ['key']
], function () {
    Route::get('/', function () {
        return view('welcome');
    });
});
