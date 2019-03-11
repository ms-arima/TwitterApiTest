<?php


Route::get('/', function () {
    return view('welcome');
});
Route::resource('/threeLeggedOauth', 'ThreeLeggedOauthController');
Route::resource('/userTimeLine', 'UserTimeLineController');
Route::resource('/followers', 'FollowersController');