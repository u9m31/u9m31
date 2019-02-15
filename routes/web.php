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

// Authentication Routes...
Route::get('/login',   'Auth\LoginController@showLoginForm') -> name('login.show');
Route::post('/login',  'Auth\LoginController@login')         -> name('login');
Route::post('/logout', 'Auth\LoginController@logout')        -> name('logout');

// Admin
Route::GROUP(['middleware' => ['auth', 'can:admin']], function() {
    Route::prefix('/api/admin/') -> name('admin.') -> group(function() {

        // USER
        Route::name('user.') -> group(function() {
            Route::post('user',          'UserController@index')    -> name('index');
            Route::post('user/store',    'UserController@store')    -> name('store');
            Route::post('user/destroy',  'UserController@destroy')  -> name('destroy');
            Route::post('user/download', 'UserController@download') -> name('download');
            Route::post('user/upload',   'UserController@upload')   -> name('upload');
        });

        // CsvPayslip
        Route::name('csvpayslip.') -> group(function() {
            Route::post('csvpayslip/index',   'CsvPayslipController@index')   -> name('index');
            Route::post('csvpayslip/upload',  'CsvPayslipController@upload')  -> name('upload');
            Route::post('csvpayslip/delete',  'CsvPayslipController@delete')  -> name('delete');
            Route::post('csvpayslip/publish', 'CsvPayslipController@publish') -> name('publish');
        });

        // Payslip
        Route::name('payslip.') -> group(function() {
            Route::post('payslip/index',  'PayslipController@index')  -> name('index');
            Route::post('payslip/delete', 'PayslipController@delete') -> name('delete');
            Route::post('payslip/pdf',    'PayslipController@pdf')    -> name('pdf');
        });

        // Actlog
        Route::name('actlog.') -> group(function() {
            Route::post('actlog',          'ActlogController@index')    -> name('index');
            Route::post('actlog/download', 'ActlogController@download') -> name('download');
        });
    });
});

// Other
Route::get('/{any}', function () {
  return view('home');
})->middleware('auth')->where('any', '.*');
