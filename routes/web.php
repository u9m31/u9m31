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

Route::get('/', function () {
    return view('home');
})->middleware('auth');

Route::get('/pdf', 'DocumentController@downloadPdf')->name('pdf');

// Authentication Routes...
Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('/login', 'Auth\LoginController@login');
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

// Admin
Route::group( ['middleware' => ['auth', 'can:admin']], function() {

  // USER
  Route::post('/api/admin/user', 'UserController@index')->name('admin/user');
  Route::post('/api/admin/user/store', 'UserController@store')->name('admin/user/store');
  Route::post('/api/admin/user/destroy', 'UserController@destroy')->name('admin/user/destroy');
  Route::post('/api/admin/user/download', 'UserController@download')->name('admin/user/download');
  Route::post('/api/admin/user/upload', 'UserController@upload')->name('admin/user/upload');

  // Payslip
  Route::post('/api/admin/payslip/csvlist', 'PayslipController@csvlist')->name('admin/payslip/csvlist');
  Route::post('/api/admin/payslip/upload', 'PayslipController@upload')->name('admin/payslip/upload');
  Route::post('/api/admin/payslip/publish', 'PayslipController@publish')->name('admin/payslip/publish');
  Route::post('/api/admin/payslip/delete', 'PayslipController@delete')->name('admin/payslip/delete');

});

// Other
Route::get('/{any}', function () {
  return view('home');
})->middleware('auth')->where('any', '.*');
