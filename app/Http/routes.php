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

/**
 * Home page
 */
Route::get('/', 'MainController@home');
Route::get('/login', 'MainController@login');

Route::get('/pr-templates', 'TemplateController@index');
Route::get('/pr-themes', 'ThemeController@index');
Route::get('/libraries', 'LibraryController@index');
Route::post('/libraries/detach/{pageId}/{name}', 'LibraryController@detach');
Route::post('/libraries/attach/{pageId}/{name}', 'LibraryController@attach');
Route::post('/libraries', 'LibraryController@store');
Route::put('/libraries/{id}', 'LibraryController@update');
Route::delete('/libraries/{id}', 'LibraryController@delete');
Route::get('/libraries/{id}/{name}/{path}', 'LibraryController@update2');
Route::post('/projects', 'ProjectController@store');
Route::get('/projects', 'UserController@projects');
Route::get('/projects/{name}', 'UserController@project');
Route::post('/projects/{id}/save-image', 'ProjectController@saveImage');
Route::put('/projects/{id}', 'ProjectController@update');
Route::delete('/projects/delete-page/{id}', 'ProjectController@deletePage');
Route::delete('/projects/{id}', 'ProjectController@delete');

Route::get('/custom-elements', 'MainController@customElements');

Route::get('/project/{id}', 'ProjectController@findPage');


Route::get('/images/all', 'ImageController@index');
Route::post('/images/store', 'ImageController@store');
Route::delete('/images/{id}', 'ImageController@delete');
Route::post('/images/delete', 'ImageController@deleteMultiple');
Route::get('/images/store/{id}/{folder}', 'ImageController@store_test');

Route::get('/folders/all', 'FolderController@index');
Route::post('/folders', 'FolderController@store');
Route::delete('/folders/{id}', 'FolderController@delete');

Route::get('/export/page/{id}', 'ExportController@exportPage');
Route::get('/export/project/{id}', 'ExportController@exportProject');
Route::get('/export/image/{path}', 'ExportController@exportImage');

Route::get('/lib/{id}', 'ProjectController@lib');
Route::get('/show_project/{id}', 'ProjectController@showProject');
//Route::get('/project/{name}/{template}', 'ProjectController@store_test');
Route::get('/libraries/detach/{pageId}/{name}', 'LibraryController@attach');
Route::get('/libraries/attach/{pageId}/{name}', 'LibraryController@attach');
//Route::get('/lib/del/{id}', 'LibraryController@delete_test');



/**
 * Authentication
 */
Route::controllers([
    'auth'     => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);



// login
//Route::get('/login', array(
//        'as'   => 'admin.login',
//        'uses' => 'Auth\AuthController@getLogin')
//);
//Route::get('/logout', array(
//        'as'   => 'admin.login',
//        'uses' => 'Auth\AuthController@getLogout')
//);
//Route::post('/login', array('as' => 'admin.login.post', 'uses' => 'Auth\AuthController@postLogin'));
