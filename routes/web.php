<?php

use App\Http\Middleware\AdminCheck;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::prefix('app')->middleware([AdminCheck::class])->group(function(){

    Route::get('/get_tags', 'AdminController@get_tags');
    Route::post('/create_tag', 'AdminController@addTag');
    Route::post('/edit_tag', 'AdminController@editTag');
    Route::post('/delete_tag', 'AdminController@deleteTag');

    Route::post('/upload', 'AdminController@upload');
    Route::post('/delete_image', 'AdminController@deleteImage');
    Route::post('/create_category', 'AdminController@addCategory');
    Route::get('/get_category', 'AdminController@getCategory');
    Route::post('/edit_category', 'AdminController@editCategory');
    Route::post('/delete_category', 'AdminController@deleteCategory');

    Route::get('/get_users', 'AdminController@getUsers');
    Route::post('/create_user', 'AdminController@createUser');
    Route::post('/edit_user', 'AdminController@editUser');

    Route::post('/admin_login', 'AdminController@adminLogin');

    //role route
    Route::post('/create_role', 'AdminController@addRole');
    Route::get('/get_roles', 'AdminController@getRoles');
    Route::post('/edit_role', 'AdminController@editRole');
    Route::post('/delete_role', 'AdminController@deleteRole');

    
});


Route::get('/logout', 'AdminController@logout');
Route::get('/', 'AdminController@index');
Route::any('{slug}', 'AdminController@index')->where('slug', '([A-z\d-\/_.]+)?');


// Route::any('{slug}', function(){
//     return view('welcome');
// });

// Route::view('/', 'welcome');

// Route::any('{slug}', 'AdminController@index')->where('slug', '([A-z\d-\/_.]+)?');