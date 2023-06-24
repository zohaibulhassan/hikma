<?php

namespace App\Http\Controllers\Backend\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Utils\Activity\SaveActivityLogController;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\Datatables\Datatables;
use App\Models\Role;
use App\Models\User;
use App\Models\SubCourses;
use App\Models\Courses;
use Auth;
use Config;
use File;
use App\Exports\StudentsDataExport;
use Illuminate\Support\Facades\Mail;
use YourCustomMailClass;

class UsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     * More info DataTables : https://yajrabox.com/docs/laravel-datatables/master
     *
     * @param Datatables $datatables
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function SubCourseUser(Request $request)
    {
        // echo 'SubCourseUser';
        // exit;
        $data['sub_courses'] = SubCourses::where("courses_id", $request->courses_id)
            ->get(["name", "id"]);

        return response()->json($data);
    }
    public function index(Datatables $datatables)
    {
        $columns = [
            'checkbox' => ['title' => '<input type="checkbox" id="select-all-checkbox">'],
            'id' => [
                'title' => 'No.',
                'orderable' => false,
                'searchable' => false,
                'render' => function () {
                    return 'function(data,type,fullData,meta){return meta.settings._iDisplayStart+meta.row+1;}';
                }
            ],
            'image',
            'name',
            'email',
            'editor' => ['title' => 'Role'],
            // 'role',
            'created_at',
            'updated_at',
            'action' => ['orderable' => false, 'searchable' => false]
        ];

        if ($datatables->getRequest()->ajax()) {
            return $datatables->of(User::all())
                ->addColumn('checkbox', function () {
                    return '<input type="checkbox" class="user-checkbox">';
                })
                ->addColumn('image', function (User $data) {
                    $getAssetFolder = asset('uploads/' . $data->image);
                    return '<img src="' . $getAssetFolder . '" width="30px" class="img-circle elevation-2">';
                })
                ->addColumn('action', function (User $data) {
                    $routeEdit = route($this->getRoute() . '.edit', $data->id);
                    $routeDelete = route($this->getRoute() . '.delete', $data->id);

                    // Check is administrator
                    if (Auth::user()->hasRole('administrator')) {
                        $button = '<a href="' . $routeEdit . '"><button class="btn btn-primary"><i class="fa fa-edit"></i></button></a> ';
                        $button .= '<a href="' . $routeDelete . '" class="delete-button"><button class="btn btn-danger"><i class="fa fa-trash"></i></button></a>';
                    } else {
                        $button = '<a href="#"><button class="btn btn-primary disabled"><i class="fa fa-edit"></i></button></a> ';
                        $button .= '<a href="#"><button class="btn btn-danger disabled"><i class="fa fa-trash"></i></button></a>';
                    }
                    return $button;
                })
                ->addColumn('role', function (User $user) {
                    return Role::where('id', $user->role)->first()->display_name;
                })
                ->rawColumns(['checkbox', 'action', 'image', 'intro'])
                ->toJson();
        }

        $html = $datatables->getHtmlBuilder()
            ->columns($columns)
            ->parameters([
                'responsive' => true,
                'autoWidth' => false,
                'lengthMenu' => [
                    [10, 25, 50, -1],
                    ['10 rows', '25 rows', '50 rows', 'Show all']
                ],
                'dom' => 'Bfrtip',
                'buttons' => ['pageLength', 'csv', 'excel', 'pdf', 'print'],
            ]);

        return view('backend.users.index', compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function add()
    {
        $data = new User();
        $data->form_action = $this->getRoute() . '.create';
        // Add page type here to indicate that the form.blade.php is in 'add' mode
        $data->page_type = 'add';
        $data->button_text = 'Add';

        if (Auth::user()->hasRole('administrator')) {
            return view('backend.users.form', [
                'data' => $data,
                'role' => Role::orderBy('id')->pluck('display_name', 'id'),
                'courses' => Courses::orderBy('id')->pluck('name', 'id'),
                'subcourses' => SubCourses::orderBy('id')->pluck('name', 'id'),
            ]);
        }

        $courses = Courses::get();
        return view('backend.users.form', [
            'data' => $data,
            'role' => Role::whereNotIn('id', [1, 2])->orderBy('id')->pluck('display_name', 'id'),
            'courses' => Courses::whereNotIn('id', [1, 2])->orderBy('id')->pluck('name', 'id'),
            'subcourses' => SubCourses::orderBy('id')->pluck('name', 'id'),
        ]);
    }

    /**
     * Get named route depends on which user is logged in
     *
     * @return String
     */
    private function getRoute()
    {
        return 'users';
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $new = $request->all();
        // $selectedSubCourses = request()->get('sub_courses_id');
        // $selectedCourses = request()->get('courses_id');
        // $subCoursesString = implode(',', $selectedSubCourses);
        $existingUser = User::where('email', $new['email'])->first();
     
            
                
            if ($existingUser) {
                
               if (($existingUser->courses_id == $new['courses_id'])  && ($existingUser->email == $new['email']) && ($existingUser->sub_courses_id == $new['sub_courses_id'])  ) {

                return redirect()->route($this->getRoute())->with('error', "Failed to create user");

                
                 } 
                else if (($existingUser->courses_id != $new['courses_id']) && ($existingUser->email == $new['email']) && ($existingUser->sub_courses_id == $new['sub_courses_id'])) {
               
                   return redirect()->route($this->getRoute())->with('error', "Failed to create user");
                
                 } 
                 else if (($existingUser->courses_id != $new['courses_id']) && ($existingUser->email == $new['email']) && ($existingUser->sub_courses_id != $new['sub_courses_id'])) {

                return redirect()->route($this->getRoute())->with('error', "Failed to create user");}

               else if (($existingUser->courses_id == $new['courses_id']) && ($existingUser->email == $new['email']) && ($existingUser->sub_courses_id != $new['sub_courses_id'])) {

                $obj = new User();
                $obj->name = $new['name'];
                $obj->email = $existingUser->email;
                $obj->password = $existingUser->password;
                $obj->plain_password = $existingUser->plain_password;
                $obj->role = $new['role'];
                $obj->courses_id = $existingUser['courses_id'];
                $obj->sub_courses_id = $new['sub_courses_id'][0];
                $obj->editor = $existingUser->editor;


                $obj->save();

                return redirect()->route($this->getRoute())->with('success', "User Already added enroll into new course successfully");
                
               }
               else{
                $obj = new User();
                $obj->name = $new['name'];
                $obj->email = $new['email'];
                $obj->password = bcrypt($new['password']);
                $obj->plain_password = $new['password'];
                $obj->role = $new['role'];
                $obj->courses_id = $new['courses_id'];
                $obj->sub_courses_id = $new['sub_courses_id'][0];
                $obj->editor = $new['editor'];

                $obj->save();
                return redirect()->route($this->getRoute())->with('success', "User Added Successfully");
               }
               
      
    }
    else{
           
            $obj = new User();
            $obj->name = $new['name'];
            $obj->email = $new['email'];
            $obj->password = bcrypt($new['password']);
            $obj->plain_password = $new['password'];
            $obj->role = $new['role'];
            $obj->courses_id = $new['courses_id'];
            $obj->sub_courses_id = $new['sub_courses_id'][0];
            $obj->editor = $new['editor'];

            $obj->save();
            return redirect()->route($this->getRoute())->with('success', "User Added Successfully");
          
    }
}




    /**
     * Validator data.
     *
     * @param array $data
     * @param $type
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data, $type)
    {
        // Determine if password validation is required depending on the calling
        return Validator::make($data, [
            // Add unique validation to prevent for duplicate email while forcing unique rule to ignore a given ID
            'email' => $type == 'create' ? 'email|required|string|max:255|unique:users' : 'required|string|max:255|unique:users,email,' . $data['id'],
            // (update: not required, create: required)
            'password' => $type == 'create' ? 'required|string|min:6|max:255' : '',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */

    public function usersDump()
    {
        $users = User::where('role', 3)->get();
        $courses = Courses::all();
        $subcourses = SubCourses::all();

        // Prepare the data for export
        $data = [];
        foreach ($users as $user) {
            $coursename = Courses::select('name')->where('id', $user->courses_id)->pluck('name')->first();
            $subcoursename = SubCourses::select('name')->where('id', $user->sub_courses_id)->pluck('name')->first();

            // $decryptedPassword = Hash::check('plain_text_password', $user->password) ? 'plain_text_password' : 'Cannot decrypt';

            $data[] = [
                'id' => $user->id,
                'Name' => $user->name,
                'Email' => $user->email,
                'Password' => $user->plain_password,
                'Course Name' => $coursename,
                'Subcourse Name' => $subcoursename,
            ];
        }

        // Prepare the legend data
        $legend = [];
        foreach ($courses as $course) {
            $subcourseNames = $subcourses->where('courses_id', $course->id)->pluck('name')->toArray();
            $legend[$course->name] = $subcourseNames;
        }

        $export = new StudentsDataExport($data, $legend);
        $fileName = 'StudentsDataExport.xlsx';

        return Excel::download($export, $fileName);
    }


    public function email()
    {
        $emailDatas = User::select('name', 'email', 'sub_courses_id')->where('role', 3)->get();
        $emailData = [];

        foreach ($emailDatas as $user) {
            $name = $user->name;
            $email = $user->email;
            $subCoursesId = $user->sub_courses_id;

            $subCourse = SubCourses::find($subCoursesId);
            $course = null;

            if ($subCourse) {
                $course = Courses::find($subCourse->id);
            }

            $emailData[] = [
                'name' => $name,
                'email' => $email,
                'subCourse' => $subCourse ? $subCourse->name : null,
                'course' => $course ? $course->name : null
            ];
        }

        $courses = Courses::all();
        $subCourses = SubCourses::all();

        $emailDataJson = json_encode($emailData); // Convert emailData array to JSON

        return view('backend.users.email', compact('courses', 'subCourses', 'emailDataJson'));
    }


    public function fetchEmails(Request $request)
    {
        $courseId = $request->input('courseId');

        // Fetch emails based on the selected course ID and role 3
        $emails = User::select('email')
            ->where('sub_courses_id', $courseId)
            ->where('role', 3)
            ->pluck('email')
            ->toArray();

        // Return the emails as JSON response
        return response()->json($emails);
    }

    public function emailsend(Request $request)
    {
    
        // Get the selected emails, subject, and editor content from the request
        $selectedEmails = $request->input('selectedEmails');
        $subject = $request->input('subject');
        $editorContent = $request->input('editorContent');

        // Loop through the selected emails and send an email to each one
        foreach ($selectedEmails as $email) {
            // Build the email content
            $user = User::where('email', $email)->first();
            $subcoursename = User::select('sub_courses_id')->where('email', $email)->first();
            $coursename = User::select('courses_id')->where('email', $email)->first();
            $subcourse = SubCourses::select('name')->where('id', $subcoursename->sub_courses_id)->pluck('name')->first();
            $course = Courses::select('name')->where('id', $coursename->courses_id)->pluck('name')->first();
            $body = str_replace('{email}', $user->email, $editorContent);
            $body = str_replace('{course}', $subcourse, $body);
            $body = str_replace('{subcourse}', $course, $body);
            $body = str_replace('{password}', $user->plain_password, $body);
            // Send the email
            Mail::to($email)->send(new \App\Mail\YourCustomMailClass($subject, $body));
             
        }
 
        return response()->json(['message' => 'Email sent successfully.']);
    }





    // ...

    public function blukUpload(Request $request)
    {
        $request->validate([
            'attachment' => 'required|mimes:xlsx,xls|max:2048' // Adjust max file size if needed
        ]);

        // Get the uploaded file
        $file = $request->file('attachment');

        $data = Excel::toCollection(null, $file)[0]; // Assuming the data is in the first sheet

        // Remove the header row
        $data->shift();

        foreach ($data as $row) {
            $email = $row[2];
            $courseName = $row[4];
            $subCourseName = $row[5];

            if (!empty($email) && !empty($courseName) && !empty($subCourseName)) {
                $user = User::where('email', $email)->first();

                if ($user) {
                    $course = Courses::where('name', $courseName)->first();
                    $subCourse = SubCourses::where('name', $subCourseName)->first();

                    if ($course && $subCourse) {
                        $user->courses_id = $course->id;
                        $user->sub_courses_id = $subCourse->id;
                        $user->editor = 'student';
                        $user->image = 'default-user.png'; // Set the value of the editor column to 'student'
                        $user->save();
                    }
                } else {
                    $user = new User();
                    $user->name = $row[1];
                    $user->email = $email;
                    $course = Courses::where('name', $courseName)->first();

                    if ($course) {
                        $user->courses_id = $course->id;
                        $user->role = 3;
                        $user->editor = 'student';
                        $user->image = 'default-user.png'; // Set the value of the editor column to 'student'

                        $password = $row[3]; // Generate a random password
                        $user->password = bcrypt($password); // Store hashed password in the users table
                        $user->plain_password = $password; // Store plain password in the plain_password column
                        $user->save();
                    }

                    $subCourse = SubCourses::where('name', $subCourseName)->first();

                    if ($course && $subCourse) {
                        $newUser = new User();
                        $newUser->name = $row[1];
                        $newUser->email = $email;
                        $newUser->courses_id = $course->id;
                        $newUser->sub_courses_id = $subCourse->id;
                        $newUser->role = 3;
                        $newUser->editor = 'student';
                        $newUser->image = 'default-user.png'; // Set the value of the editor column to 'student'
                        $newUser->password = $user->password; // Set the same password as the previous user
                        $newUser->plain_password = $user->plain_password; // Set the same plain password as the previous user
                        $newUser->save();
                    }
                }
            }
        }

        return redirect('users')->with('success', 'Users imported successfully.');
    }









    public function edit($id)
    {
        $data = User::find($id);
        $data->form_action = $this->getRoute() . '.update';
        // Add page type here to indicate that the form.blade.php is in 'edit' mode
        $data->page_type = 'edit';
        $data->button_text = 'Edit';

        if (Auth::user()->hasRole('administrator')) {
            return view('backend.users.form', [
                'data' => $data,
                'role' => Role::orderBy('id')->pluck('display_name', 'id'),
                'courses' => Courses::orderBy('id')->pluck('name', 'id'),
                'subcourses' => SubCourses::orderBy('id')->pluck('name', 'id'),

            ]);
        }

        return view('backend.users.form', [
            'data' => $data,
            'role' => Role::whereNotIn('id', [1, 2])->orderBy('id')->pluck('display_name', 'id'),
            'courses' => Courses::whereNotIn('id', [1, 2])->orderBy('id')->pluck('name', 'id'),
            'subcourses' => SubCourses::orderBy('id')->pluck('name', 'id'),

        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $new = $request->all();
        try {
            $currentData = User::find($request->get('id'));
            if ($currentData) {
                $this->validator($new, 'update')->validate();

                if (!$new['password']) {
                    $new['password'] = $currentData['password'];
                } else {
                    $new['password'] = bcrypt($new['password']);
                }

                if ($currentData->role != $new['role']) {
                    $currentData->roles()->sync($new['role']);
                }

                // check delete flag: [name ex: image_delete]
                if ($request->get('image_delete') != null) {
                    $new['image'] = null; // filename for db

                    if ($currentData->{'image'} != 'default-user.png') {
                        @unlink(Config::get('const.UPLOAD_PATH') . $currentData['image']);
                    }
                }

                // if new image is being uploaded
                // upload image
                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    // image file name example: [id]_image.jpg
                    ${'image'} = $currentData->id . "_image." . $file->getClientOriginalExtension();
                    $new['image'] = ${'image'};
                    // save image to the path
                    $file->move(Config::get('const.UPLOAD_PATH'), ${'image'});
                } else {
                    $new['image'] = 'default-user.png';
                }

                $selectedSubcourses = $request->get('sub_courses_id');
                $subcoursesString = implode(',', $selectedSubcourses);
                $currentData->sub_courses_id = $subcoursesString;
                $selectedcourses = $request->get('courses_id');
                $currentData->courses_id = $selectedcourses;
                // Update
                $currentData->update($new);

                // Save log
                $controller = new SaveActivityLogController();
                $controller->saveLog($new, "Update user");

                return redirect()->route($this->getRoute())->with('success', Config::get('const.SUCCESS_UPDATE_MESSAGE'));
            }

            // If update is failed
            return redirect()->route($this->getRoute())->with('error', Config::get('const.FAILED_UPDATE_MESSAGE'));
        } catch (Exception $e) {
            // If update is failed
            return redirect()->route($this->getRoute())->with('error', Config::get('const.FAILED_CREATE_MESSAGE'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        try {
            if (Auth::user()->id != $id) {

                // delete
                $user = User::find($id);
                $user->detachRole($id);

                // Delete the image
                if ($user->{'image'} != 'default-user.png') {
                    @unlink(Config::get('const.UPLOAD_PATH') . $user['image']);
                }

                // Delete the data DB
                $user->delete();

                // Save log
                $controller = new SaveActivityLogController();
                $controller->saveLog($user->toArray(), "Delete user");

                //delete success
                return redirect()->route($this->getRoute())->with('success', Config::get('const.SUCCESS_DELETE_MESSAGE'));
            }
            // delete failed
            return redirect()->route($this->getRoute())->with('error', Config::get('const.FAILED_DELETE_SELF_MESSAGE'));
        } catch (Exception $e) {
            // delete failed
            return redirect()->route($this->getRoute())->with('error', Config::get('const.FAILED_DELETE_MESSAGE'));
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route($this->getRoute())->with('error', Config::get('const.ERROR_FOREIGN_KEY'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function import()
    {
        $data = new User();
        $data->form_action = $this->getRoute() . '.importData';
        // Add page type here to indicate that the form.blade.php is in 'add' mode
        $data->page_type = 'add';
        $data->button_text = 'Import';

        return view('backend.users.import', [
            'data' => $data,
        ]);
    }

    /**
     * Upload and import data from csv file.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function importData(Request $request)
    {
        $errorMessage = '';
        $errorArr = array();

        // If file extension is 'csv'
        if ($request->hasFile('import')) {
            $file = $request->file('import');

            // File Details
            $extension = $file->getClientOriginalExtension();

            // If file extension is 'csv'
            if ($extension == 'csv') {
                $fp = fopen($file, 'rb');

                $header = fgetcsv($fp, 0, ',');
                $countheader = count($header);

                // Check is csv file is correct format
                if ($countheader < 6 && in_array('email', $header, true) && in_array('first_name', $header, true) && in_array('last_name', $header, true) && in_array('role', $header, true) && in_array('editor', $header, true) && in_array('password', $header, true)) {
                    // Loop the row data csv
                    while (($csvData = fgetcsv($fp)) !== false) {
                        $csvData = array_map('utf8_encode', $csvData);

                        // Row column length
                        $dataLen = count($csvData);

                        // Skip row if length != 5
                        if (!($dataLen == 5)) {
                            continue;
                        }

                        // Assign value to variables
                        $email = trim($csvData[0]);
                        $first_name = trim($csvData[1]);
                        $last_name = trim($csvData[2]);
                        $name = $first_name . ' ' . $last_name;
                        $role = trim($csvData[3]);
                        $editor = trim($csvData[4]);

                        // Insert data to users table
                        // Check if any duplicate email
                        if ($this->checkDuplicate($email, 'email')) {
                            $errorArr[] = $email;
                            $str = implode(", ", $errorArr);
                            $errorMessage = '-Some data email already exists ( ' . $str . ' )';
                            continue;
                        }

                        $password = trim($csvData[4]);
                        $hashed = bcrypt($password);

                        $data = array(
                            'email' => $email,
                            'name' => $name,
                            'role' => $role,
                            'editor' => $editor,
                            'password' => $hashed,
                            'image' => 'default-user.png',
                        );

                        // create the user
                        $createNew = User::create($data);

                        // Attach role
                        $createNew->roles()->attach($role);

                        // Save user
                        $createNew->save();
                    }

                    if ($errorMessage == '') {
                        return redirect()->route($this->getRoute())->with('success', 'Imported was success!');
                    }
                    return redirect()->route($this->getRoute())->with('warning', 'Imported was success! <br><b>Note: We do not import this data data because</b><br>' . $errorMessage);
                }
                return redirect()->route($this->getRoute())->with('error', 'Import failed! You are using the wrong CSV format. Please use the CSV template to import your data.');
            }
            return redirect()->route($this->getRoute())->with('error', 'Please choose file with .CSV extension.');
        }

        return redirect()->route($this->getRoute())->with('error', 'Please select CSV file.');
    }

    /**
     * Function check email is exist or not.
     *
     * @param $data
     * @param $typeCheck
     * @return bool
     */
    public function checkDuplicate($data, $typeCheck)
    {
        if ($typeCheck == 'email') {
            $isExists = User::where('email', $data)->first();
        }

        if ($typeCheck == 'name') {
            $isExists = History::where('name', $data)->first();
        }

        if ($isExists) {
            return true;
        }

        return false;
    }

    public function teacher(Datatables $datatables)
    {
        $columns = [
            'id' => [
                'title' => 'No.',
                'orderable' => false,
                'searchable' => false,
                'render' => function () {
                    return 'function(data,type,fullData,meta){return meta.settings._iDisplayStart+meta.row+1;}';
                }
            ],
            'image',
            'name',
            'email',
            'editor',
            'created_at',
            'updated_at',
            'action' => ['orderable' => false, 'searchable' => false]
        ];

        if ($datatables->getRequest()->ajax()) {
            return $datatables->of(User::where('editor', 'teacher'))
                ->addColumn('image', function (User $data) {
                    $getAssetFolder = asset('uploads/' . $data->image);
                    return '<img src="' . $getAssetFolder . '" width="30px" class="img-circle elevation-2">';
                })
                ->addColumn('action', function (User $data) {
                    $routeEdit = route($this->getRoute() . '.edit', $data->id);
                    $routeDelete = route($this->getRoute() . '.delete', $data->id);

                    // Check is administrator
                    if (Auth::user()->hasRole('administrator')) {
                        $button = '<a href="' . $routeEdit . '"><button class="btn btn-primary"><i class="fa fa-edit"></i></button></a> ';
                        $button .= '<a href="' . $routeDelete . '" class="delete-button"><button class="btn btn-danger"><i class="fa fa-trash"></i></button></a>';
                    } else {
                        $button = '<a href="#"><button class="btn btn-primary disabled"><i class="fa fa-edit"></i></button></a> ';
                        $button .= '<a href="#"><button class="btn btn-danger disabled"><i class="fa fa-trash"></i></button></a>';
                    }
                    return $button;
                })
                ->addColumn('role', function (User $user) {
                    return Role::where('id', $user->role)->first()->display_name;
                })
                ->rawColumns(['action', 'image', 'intro'])
                ->toJson();
        }

        $html = $datatables->getHtmlBuilder()
            ->columns($columns)
            ->parameters([
                'responsive' => true,
                'autoWidth' => false,
                'lengthMenu' => [
                    [10, 25, 50, -1],
                    ['10 rows', '25 rows', '50 rows', 'Show all']
                ],
                'dom' => 'Bfrtip',
                'buttons' => ['pageLength', 'csv', 'excel', 'pdf', 'print'],
            ]);

        return view('backend.users.index', compact('html'));
    }

    public function student(Datatables $datatables)
    {
        $columns = [
            'id' => [
                'title' => 'No.',
                'orderable' => false,
                'searchable' => false,
                'render' => function () {
                    return 'function(data,type,fullData,meta){return meta.settings._iDisplayStart+meta.row+1;}';
                }
            ],
            'image',
            'name',
            'email',
            'editor',
            'created_at',
            'updated_at',
            'action' => ['orderable' => false, 'searchable' => false]
        ];

        if ($datatables->getRequest()->ajax()) {
            return $datatables->of(User::where('editor', 'student'))
                ->addColumn('image', function (User $data) {
                    $getAssetFolder = asset('uploads/' . $data->image);
                    return '<img src="' . $getAssetFolder . '" width="30px" class="img-circle elevation-2">';
                })
                ->addColumn('action', function (User $data) {
                    $routeEdit = route($this->getRoute() . '.edit', $data->id);
                    $routeDelete = route($this->getRoute() . '.delete', $data->id);

                    // Check is administrator
                    if (Auth::user()->hasRole('administrator')) {
                        $button = '<a href="' . $routeEdit . '"><button class="btn btn-primary"><i class="fa fa-edit"></i></button></a> ';
                        $button .= '<a href="' . $routeDelete . '" class="delete-button"><button class="btn btn-danger"><i class="fa fa-trash"></i></button></a>';
                    } else {
                        $button = '<a href="#"><button class="btn btn-primary disabled"><i class="fa fa-edit"></i></button></a> ';
                        $button .= '<a href="#"><button class="btn btn-danger disabled"><i class="fa fa-trash"></i></button></a>';
                    }
                    return $button;
                })
                ->addColumn('role', function (User $user) {
                    return Role::where('id', $user->role)->first()->display_name;
                })
                ->rawColumns(['action', 'image', 'intro'])
                ->toJson();
        }

        $html = $datatables->getHtmlBuilder()
            ->columns($columns)
            ->parameters([
                'responsive' => true,
                'autoWidth' => false,
                'lengthMenu' => [
                    [10, 25, 50, -1],
                    ['10 rows', '25 rows', '50 rows', 'Show all']
                ],
                'dom' => 'Bfrtip',
                'buttons' => ['pageLength', 'csv', 'excel', 'pdf', 'print'],
            ]);

        return view('backend.users.index', compact('html'));
    }
}