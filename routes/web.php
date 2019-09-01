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
Route::group(['middleware' => ['auth', 'role:admin']], function () {
	Route::get('/', 'HomeController@index')->name('home');

	//seed routes
	Route::get('/seed', 'SeedController@index')->name('seed');
	Route::get('/seed/{id}', 'SeedController@show')->name('seed.show');
	Route::post('/seed/store', 'SeedController@store')->name('seed.store');
	Route::get('/seed/export/{id}', 'SeedController@export')->name('seed.export');
	Route::get('/seed/process/{id}', 'DomainController@chunkProcessAjax')->name('seed.process');
	Route::delete('/seed/delete/{id}', 'SeedController@destroy')->name('seed.delete');

	//domain routes
	Route::get('/domain', 'DomainController@index')->name('domain');
	Route::get('/domain/{id}', 'DomainController@show')->name('domain.show');
	// Route::post('/domain/store', 'DomainController@store')->name('domain.store');
	// Route::delete('/domain/delete/{id}', 'DomainController@destroy')->name('domain.delete');
	Route::post('/domain/process/chunk/', 'DomainController@process')->name('domain.process.chunk');


	Route::get('/jobs', 'JobController@index')->name('jobs');
	//Route::get('/domain/{id}', 'DomainController@show')->name('domain.show');

	//test
	Route::get('/test/{domain}', 'DomainController@testFindJobsPage')->name('domain.test');
	//Route::get('/testing/jobdetails', 'DomainController@testFindJobDetails')->name('domain.test.jobtetails');


	//instructions
	Route::get('/instructions', function(){
		return view('admin.instructions');
	})->name('instructions');



	//temp

	Route::get('/ccrt', function(){
		//$admin = Role::create( ['name' => 'admin']);
    //$guest = Role::create( ['name' => 'guest']);

		//$man = User::find(Auth::user()->id);
		//$man->assignRole('guest');
		$ret = new stdClass();
		$ret->length = 'hola';
		echo $ret->length;
	});

});
