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
    return view('auth.login');
});

Auth::routes();



Route::GET('fileattachment', 'AttachmentController@index')->name('fileattachment');

Route::POST('upload-attachment', 'AttachmentController@uploadAttachment')->name('uploadattachment');






Route::get('/home', 'HomeController@index')->name('home');
Route::GET('/privacy', 'Backend\Policy\PolicyController@index');
/*
|--------------------------------------------------------------------------
| administrator
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => ['role:administrator']], function () {
    Route::GET('/users', 'Backend\Users\UsersController@index')->name('users');
    Route::GET('/users/teacher', 'Backend\Users\UsersController@teacher')->name('teacher');
    Route::GET('/users/student', 'Backend\Users\UsersController@student')->name('student');
    Route::GET('/users/add', 'Backend\Users\UsersController@add')->name('users.add');
    Route::POST('/users/create', 'Backend\Users\UsersController@create')->name('users.create');
    Route::GET('/users/edit/{id}', 'Backend\Users\UsersController@edit')->name('users.edit');
    Route::POST('/users/update', 'Backend\Users\UsersController@update')->name('users.update');
    Route::GET('/users/delete/{id}', 'Backend\Users\UsersController@delete')->name('users.delete');
    Route::GET('/users/import', 'Backend\Users\UsersController@import')->name('users.import');
    Route::POST('/users/importData', 'Backend\Users\UsersController@importData')->name('users.importData');
    Route::GET('/users/usersDump', 'Backend\Users\UsersController@usersDump')->name('users.usersDump');
    Route::POST('/users/blukUpload', 'Backend\Users\UsersController@blukUpload')->name('users.blukUpload');

    Route::GET('/users/email', 'Backend\Users\UsersController@email')->name('users.email');

    Route::get('users/email-data', 'Backend\Users\UsersController@getEmailData')->name('users.email-data');

    Route::get('users/fetch-emails', 'Backend\Users\UsersController@fetchEmails')->name('users.fetch-emails');

    Route::post('users/emailsend', 'Backend\Users\UsersController@emailsend')->name('users/emailsend');
    Route::get('users/getmail', 'Backend\Users\UsersController@getmail')->name('users/getmail');
    //user course
    Route::POST('/sub-course-user', 'Backend\Users\UsersController@SubCourseUser');
    //end 


    // Courses
    Route::GET('/courses', 'Backend\Courses\CoursesController@index')->name('courses');
    Route::GET('/courses/card', 'Backend\Courses\CoursesController@card')->name('card');
    Route::GET('/courses/card/{id}', 'Backend\Courses\CoursesController@cardby');
    Route::GET('/fulldata', 'Backend\Courses\CoursesController@fulldata');
    Route::GET('/courses/schedule/{id}', 'Backend\Courses\CoursesController@ScheduleBy');
    Route::GET('/courses/add', 'Backend\Courses\CoursesController@add')->name('courses.add');
    Route::POST('/courses/create', 'Backend\Courses\CoursesController@create')->name('courses.create');
    Route::GET('/courses/edit/{id}', 'Backend\Courses\CoursesController@edit')->name('courses.edit');
    Route::POST('/courses/update', 'Backend\Courses\CoursesController@update')->name('courses.update');
    Route::GET('/courses/delete/{id}', 'Backend\Courses\CoursesController@delete')->name('courses.delete');
    Route::GET('/courses/subcourseExcel/{id}', 'Backend\Courses\CoursesController@subcourseExcel');
    Route::GET('courses/BlukUpload', 'Backend\Courses\CoursesController@BlukUpload');
    Route::POST('courses/BlukUploaddata', 'Backend\Courses\CoursesController@BlukUploaddata');


    // Sub Courses
    Route::GET('/sub-courses', 'Backend\SubCourses\SubCoursesController@index')->name('subcourses');
    Route::GET('/sub-courses/card', 'Backend\SubCourses\SubCoursesController@card')->name('card');
    Route::GET('/sub-courses/add', 'Backend\SubCourses\SubCoursesController@add')->name('subcourses.add');
    Route::POST('/sub-courses/create', 'Backend\SubCourses\SubCoursesController@create')->name('subcourses.create');
    Route::GET('/sub-courses/edit/{id}', 'Backend\SubCourses\SubCoursesController@edit')->name('subcourses.edit');
    Route::POST('/sub-courses/update', 'Backend\SubCourses\SubCoursesController@update')->name('subcourses.update');
    Route::GET('/sub-courses/delete/{id}', 'Backend\SubCourses\SubCoursesController@delete')->name('subcourses.delete');

    // Timing
    Route::GET('/time', 'Backend\Times\TimesController@index')->name('time');
    Route::GET('/time/card', 'Backend\Times\TimesController@card')->name('card');
    Route::GET('/time/add', 'Backend\Times\TimesController@add')->name('time.add');
    Route::POST('/time/create', 'Backend\Times\TimesController@create')->name('time.create');
    Route::GET('/time/edit/{id}', 'Backend\Times\TimesController@edit')->name('time.edit');
    Route::POST('/time/update', 'Backend\Times\TimesController@update')->name('time.update');
    Route::GET('/time/delete/{id}', 'Backend\Times\TimesController@delete')->name('time.delete');







    Route::GET('/settings', 'Backend\Setting\SettingsController@index')->name('settings');
    Route::POST('/settings/update', 'Backend\Setting\SettingsController@update')->name('settings.update');

    Route::GET('/areas', 'Backend\Area\AreaController@index')->name('areas');
    Route::GET('/areas/add', 'Backend\Area\AreaController@add')->name('areas.add');
    Route::POST('/areas/create', 'Backend\Area\AreaController@create')->name('areas.create');
    Route::GET('/areas/edit/{id}', 'Backend\Area\AreaController@edit')->name('areas.edit');
    Route::POST('/areas/update', 'Backend\Area\AreaController@update')->name('areas.update');
    Route::GET('/areas/delete/{id}', 'Backend\Area\AreaController@delete')->name('areas.delete');
    Route::GET('/areas/showAllDataLocation/{id}', 'Backend\Area\AreaController@showAllDataLocation')->name('areas.showAllDataLocation');
    Route::POST('/areas/storeLocation', 'Backend\Area\AreaController@storeLocation')->name('areas.storeLocation');
    Route::POST('/areas/deleteLocationTable', 'Backend\Area\AreaController@deleteLocationTable')->name('areas.deleteLocationTable');
});

/*
|--------------------------------------------------------------------------
| administrator|admin
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => ['role:administrator|admin|staff']], function () {
    Route::GET('/analytics', 'Backend\Analytic\AnalyticsController@index')->name('analytics');
});

/*
|--------------------------------------------------------------------------
| administrator|admin|editor|guest
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => ['role:administrator|admin|staff|guest']], function () {
    Route::GET('/checkProductVerify', 'MainController@checkProductVerify')->name('checkProductVerify');

    Route::GET('/profile/details', 'Backend\Profile\ProfileController@details')->name('profile.details');
    Route::POST('/profile/update', 'Backend\Profile\ProfileController@update')->name('profile.update');
});


/*
|--------------------------------------------------------------------------
| administrator|admin|staff
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => ['role:administrator|admin|staff']], function () {
    Route::GET('/attendances', 'Backend\Attendance\AttendanceController@index')->name('attendances');
    Route::GET('/attendances/student', 'Backend\Attendance\AttendanceController@student')->name('attendancesstudent');
    Route::GET('/attendances/teacher', 'Backend\Attendance\AttendanceController@teacher')->name('attendancesteacher');
});

Route::post('reinputkey/index/{code}', 'Utils\Activity\ReinputKeyController@index');
