<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);


Route::post('api/signup', 'Auth\AuthController@signup');
Route::post('api/login', 'Auth\AuthController@login');

Route::get('api/users', 'UsersController@index');
Route::get('api/users/{id}', 'UsersController@show');
Route::get('api/users/{id}/requests', 'UsersController@getRequestsByUser');

Route::get('api/threads', 'MessagesController@index');
Route::get('api/threads/participants/{id}', 'MessagesController@threadsByParticipants');
Route::post('api/threads', 'MessagesController@store');
Route::get('api/threads/{id}', 'MessagesController@show');
Route::post('api/threads/{id}', 'MessagesController@update');

Route::get('api/requests', 'RequestsController@index');
Route::get('api/requests/{id}', 'RequestsController@show');
Route::post('api/requests', 'RequestsController@store');
Route::post('api/requests/{id}', 'RequestsController@update');

Route::get('api/requestCategories', 'RequestCategoriesController@index');
