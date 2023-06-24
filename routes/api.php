<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('login', 'Api\Auth\ApiAuthController@login');
Route::get('area/index', 'Api\Area\ApiAreaController@index');

Route::post('attendance/apiSaveAttendance', 'Api\Attendance\ApiAttendanceController@apiSaveAttendance');

// get user
Route::get('getuser', 'Api\Attendance\ApiAttendanceController@GetUser');

// user change password
Route::post('changepass', 'Api\Attendance\ApiAttendanceController@UserPassChange');


// test
// Route::post('test-attendance', 'Api\Attendance\ApiAttendanceController@AttendanceTest');
// default hikma wokr
// Route::post('attendance/apiSaveAttendance', 'Api\Attendance\ApiAttendanceController@apiSaveAttendance');

// checkout Attendence
Route::post('attendance/apiCheckOutAttendance', 'Api\Attendance\ApiAttendanceController@apiCheckoutAttendance');

Route::get('/helper/{code}', function ($code) {return App\Helpers\Helper::checkingCode($code);});
Route::get('/helper', function () {return App\Helpers\Helper::getInfo();});
Route::get('/write', function () {return App\Helpers\Helper::write();});

// Route::group(['middleware' => ['role:administrator|admin|staff']], function () {
    Route::get('/attendances', 'Api\Attendance\ApiAttendanceController@attendancescs');
    Route::get('/getcourses', 'Api\Attendance\ApiAttendanceController@GetCourses');
    Route::get('/getcoursebysubbytimes', 'Api\Attendance\ApiAttendanceController@GetCourseBySubByTime');
    Route::get('/CT', 'Api\Attendance\ApiAttendanceController@CT');
// });
