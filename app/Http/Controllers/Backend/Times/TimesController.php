<?php

namespace App\Http\Controllers\Backend\Times;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Utils\Activity\SaveActivityLogController;
// use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;
use App\Models\Role;
use App\Models\User;
use App\Models\Courses;
use App\Models\SubCourses;
use App\Models\Time;
use Illuminate\Support\Facades\DB;
use Auth;
use Config;
use File;
use Validator;

class TimesController extends Controller
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
    public function card(){
       

        // $Times = Time::join('sub_courses', 'times.sub_courses_id', '=', 'sub_courses.id')
        // ->get(['times.*', 'sub_courses.name as sub_courses_id']);
        // dd($Times);
        // exit;
        $Times = Time::get();
        return view('backend/time/card')->with(compact('Times'));
    }
    public function index(Datatables $datatables)
    {
        // $a = Courses::get();
        // echo $a;
        // exit;
        $columns = [
            'id' => ['title' => 'No.', 'orderable' => false, 'searchable' => false, 'render' => function () {
                return 'function(data,type,fullData,meta){return meta.settings._iDisplayStart+meta.row+1;}';
            }],
            'sub_courses_id',
            'program',
            'day',
            'time_in',
            'time_out',
            'created_at',
            'updated_at',
            'action' => ['orderable' => false, 'searchable' => false]
        ];

        if ($datatables->getRequest()->ajax()) {
            return $datatables->of(Time::all())
                ->addColumn('action', function (Time $data) {
                    $routeEdit = route($this->getRoute() . '.edit', $data->id);
                    $routeDelete = route($this->getRoute() . '.delete', $data->id);

                    // Check is administrator
                    if (Auth::user()->hasRole('administrator')) {
                        $button = '<a href="'.$routeEdit.'"><button class="btn btn-primary"><i class="fa fa-edit"></i></button></a> ';
                        $button .= '<a href="'.$routeDelete.'" class="delete-button"><button class="btn btn-danger"><i class="fa fa-trash"></i></button></a>';
                    } else {
                        $button = '<a href="#"><button class="btn btn-primary disabled"><i class="fa fa-edit"></i></button></a> ';
                        $button .= '<a href="#"><button class="btn btn-danger disabled"><i class="fa fa-trash"></i></button></a>';
                    }
                    return $button;
                })
                ->rawColumns(['action', 'intro'])
                ->toJson();
        }

        $html = $datatables->getHtmlBuilder()
            ->columns($columns)
            ->parameters([
                'responsive' => true,
                'autoWidth' => false,
                'lengthMenu' => [
                    [ 10, 25, 50, -1 ],
                    [ '10 rows', '25 rows', '50 rows', 'Show all' ]
                ],
                'dom' => 'Bfrtip',
                'buttons' => ['pageLength', 'csv', 'excel', 'pdf', 'print'],
            ]);
        return view('backend.time.index', compact('html'));
    }


    public function teacher(Datatables $datatables)
    {
        $columns = [
            'id' => ['title' => 'No.', 'orderable' => false, 'searchable' => false, 'render' => function () {
                return 'function(data,type,fullData,meta){return meta.settings._iDisplayStart+meta.row+1;}';
            }],
            'image',
            'name',
            'email',
            'role_play',
            'created_at',
            'updated_at',
            'action' => ['orderable' => false, 'searchable' => false]
        ];

        if ($datatables->getRequest()->ajax()) {
            return $datatables->of(User::where('role_play' , 'teacher'))
                ->addColumn('image', function (User $data) {
                    $getAssetFolder = asset('uploads/' . $data->image);
                    return '<img src="'.$getAssetFolder.'" width="30px" class="img-circle elevation-2">';
                })
                ->addColumn('action', function (User $data) {
                    $routeEdit = route($this->getRoute() . '.edit', $data->id);
                    $routeDelete = route($this->getRoute() . '.delete', $data->id);

                    // Check is administrator
                    if (Auth::user()->hasRole('administrator')) {
                        $button = '<a href="'.$routeEdit.'"><button class="btn btn-primary"><i class="fa fa-edit"></i></button></a> ';
                        $button .= '<a href="'.$routeDelete.'" class="delete-button"><button class="btn btn-danger"><i class="fa fa-trash"></i></button></a>';
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
                    [ 10, 25, 50, -1 ],
                    [ '10 rows', '25 rows', '50 rows', 'Show all' ]
                ],
                'dom' => 'Bfrtip',
                'buttons' => ['pageLength', 'csv', 'excel', 'pdf', 'print'],
            ]);

        return view('backend.time.index', compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function add()
    {
        $data = new SubCourses();
        $data->form_action = $this->getRoute() . '.create';
        // Add page type here to indicate that the form.blade.php is in 'add' mode
        $data->page_type = 'add';
        $data->button_text = 'Add';

        if (Auth::user()->hasRole('administrator')) {
            return view('backend.time.form', [
                'data' => $data,
                'courses' => Courses::orderBy('id')->pluck('name', 'id'),
                'subcourses' => SubCourses::orderBy('id')->pluck('name', 'id'),
            ]);
        }

        return view('backend.time.form', [
            'data' => $data,
            'courses' => Courses::whereNotIn('id', [1, 2])->orderBy('id')->pluck('name', 'id'),
            'subcourses' => SubCourses::whereNotIn('id', [1, 2])->orderBy('id')->pluck('name', 'id'),
        ]);
    }


    /**
     * Get named route depends on which user is logged in
     *
     * @return String
     */
    private function getRoute()
    {
        return 'time';
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
        // $name = $request->input('day');

        // dd($new);
        // exit;
        $validator = Validator::make(\request()->all(), [
            'sub_courses_id' => 'required|max:50|unique:times',
            'program' => 'required|max:50',
            'day' => 'required|max:50',
            'time_in' => 'required|max:50',
            'time_in_apm' => 'required|max:20',
            'time_out' => 'required|max:50',
            'time_out_apm' => 'required|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }
        
        $selectedDays = request()->get('day');

        $dayString = implode(',', $selectedDays);
        
        Time::create([
            'sub_courses_id' => request()->get('sub_courses_id'),
            'program' => request()->get('program'),
            'day' => $dayString,
            'time_in' => request()->get('time_in'),
            'time_in_apm' => request()->get('time_in_apm'),
            'time_out' => request()->get('time_out'),
            'time_out_apm' => request()->get('time_out_apm'),
            'timezone' => 'Asia/Karachi',
        ]);
        // $selectedDays = request()->get('day');

        // foreach ($selectedDays as $day) {
        //     Time::create([
        //         'sub_courses_id' => request()->get('sub_courses_id'),
        //         'program' => request()->get('program'),
        //         'day' => $day,
        //         'time_in' => request()->get('time_in'),
        //         'time_out' => request()->get('time_out'),
        //     ]);
        // }

        // Time::create([
        //     'sub_courses_id' => request()->get('sub_courses_id'),
        //     'program' => request()->get('program'),
        //     'day[]' => request()->get('day[]'),
        //     // 'time_in' => request()->get('time_in'),
        //     // 'time_out' => request()->get('time_out'),
        // ]);

        return redirect('time')->with('success', Config::get('const.SUCCESS_CREATE_MESSAGE'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Time::find($id);
        $data->form_action = $this->getRoute() . '.update';
        // Add page type here to indicate that the form.blade.php is in 'edit' mode
        $data->page_type = 'edit';
        $data->button_text = 'Edit';

        if (Auth::user()->hasRole('administrator')) {
            return view('backend.time.form', [
                'data' => $data,
                'courses' => Courses::orderBy('id')->pluck('name', 'id'),
                'subcourses' => SubCourses::orderBy('id')->pluck('name', 'id'),
            ]);
        }
        
        return view('backend.time.form', [
            'data' => $data,
            'courses' => Courses::whereNotIn('id', [1, 2])->orderBy('id')->pluck('name', 'id'),
            'subcourses' => SubCourses::whereNotIn('id', [1, 2])->orderBy('id')->pluck('name', 'id'),
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

        $currentData = Time::find($request->get('id'));

        $validator = Validator::make(\request()->all(), [
            'sub_courses_id' => 'required|max:50|unique:times,sub_courses_id,'.$currentData->id,
            'program' => 'required|max:50|unique:times,program,'.$currentData->id,
            'day' => 'required|max:50|unique:times,day,'.$currentData->id,
            'time_in' => 'required|max:50|unique:times,time_in,'.$currentData->id,
            'time_in_apm' => 'required|max:50|unique:times,time_in_apm,'.$currentData->id,
            'time_out' => 'required|max:50|unique:times,time_out,'.$currentData->id,
            'time_out_apm' => 'required|max:50|unique:times,time_out_apm,'.$currentData->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }
        $currentData->update([
            'sub_courses_id' => request()->get('sub_courses_id'),
            'program' => request()->get('program'),
            'day' => request()->get('day'),
            'time_in' => request()->get('time_in'),
            'time_in_apm' => request()->get('time_in_apm'),
            'time_out' => request()->get('time_out'),
            'time_out_apm' => request()->get('time_out_apm'),
            'timezone' => 'Asia/Karachi',
        ]);
        return redirect('time')->with('success', Config::get('const.SUCCESS_UPDATE_MESSAGE'));;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $Courses = Time::find($id);
        $Courses->delete();
        return redirect('time')->with('success', Config::get('const.SUCCESS_DELETE_MESSAGE'));;
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
                if ($countheader < 6 && in_array('email', $header, true) && in_array('first_name', $header, true) && in_array('last_name', $header, true) && in_array('role', $header, true) && in_array('role_pllay', $header, true) && in_array('password', $header, true)) {
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
                        $role_play = trim($csvData[4]);

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
                            'role_play' => $role_play,
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
        if ($typeCheck == 'name') {
            $isExists = Courses::where('name', $data)->first();
        }

        if ($isExists) {
            return true;
        }

        return false;
    }
}
