<?php

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

Auth::routes();
// use Spatie\Permission\Models\Role;
// use Spatie\Permission\Models\Permission;
// use App\User;
Route::group(['middleware' => 'auth'], function () {
	Route::get('/', 'HomeController@index')->name('home');



	//temp

	// Route::get('/ccrt', function(){
	// 	$admin = Role::create( ['name' => 'admin']);
  //   $guest = Role::create( ['name' => 'guest']);
	//
	// 	$man = User::find(Auth::user()->id);
	// 	$man->assignRole('admin');
	//
	// });

});
