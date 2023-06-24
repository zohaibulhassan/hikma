<?php

namespace App\Http\Controllers\Backend\Courses;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Utils\Activity\SaveActivityLogController;
// use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\Datatables\Datatables;
use App\Models\Role;
use App\Models\User;
use App\Models\Courses;
use App\Models\SubCourses;
use App\Models\Time;    
use Auth;
use Config;
use File;
use Validator;
use App\Exports\CoursesDataExport;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class CoursesController extends Controller
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
    public function card()
    {

        $Courses = Courses::get();
        return view('backend/courses/card')->with(compact('Courses'));
    }

    public function cardby(Request $request, $id)
    {
        // $SubCourses = SubCourses::where('courses_id',$id)->get();
        $SubCourses = SubCourses::join('times', 'times.sub_courses_id', '=', 'sub_courses.id')
            ->where('sub_courses.courses_id', $id)
            ->get();
        // echo $SubCourses;
        // exit;
        $Courses = SubCourses::join('courses', 'sub_courses.courses_id', '=', 'courses.id')
            ->where('sub_courses.courses_id', $id)
            ->get(['sub_courses.*', 'courses.name as courses_id']);
        // $SubCourses = SubCourses::join('courses', 'sub_courses.courses_id', '=', 'courses.id')
        // ->get(['sub_courses.*', 'courses.name as courses_id']);        

        // echo $Courses;
        // exit;

        return view('backend/courses/cardby')->with(compact('Courses', 'SubCourses'));
    }




    






    public function subcourseExcel($id)
    {
        $courseID = $id;
        $coursename = Courses::where('id', $id)->pluck('name');

        $subcoursesid = SubCourses::where('courses_id', $id)->get('id')->toArray();

        if (!empty($subcoursesid)) {

            $subcourses = [];
            $programname = [];
            $day = [];
            $timein = [];
            $timeout = [];
            $timeformat = [];

            foreach ($subcoursesid as $key => $value) {
                $subcourses[] = SubCourses::where('id', $value)->pluck('name')->toArray();
                $programname[] = Time::where('sub_courses_id', $value)->pluck('program')->toArray();
                $day[] = Time::where('sub_courses_id', $value)->pluck('day')->all();
                $timein[] = Time::where('sub_courses_id', $value)->pluck('time_in')->all();
                $timeout[] = Time::where('sub_courses_id', $value)->pluck('time_out')->all();
                if (Time::where('sub_courses_id', $value)->pluck('time_in_apm') == Time::where('sub_courses_id', $value)->pluck('time_out_apm')) {
                    $timeformat[] = Time::where('sub_courses_id', $value)->pluck('time_in_apm');
                } else {
                    $timeformat[] = null;
                }
            }

            $data = [
                'courseID' => $courseID,
                'coursename' => $coursename,
                'subcoursesid' => $subcoursesid,
                'subcourses' => $subcourses,
                'programname' => $programname,
                'day' => $day,
                'timein' => $timein,
                'timeout' => $timeout,
                'timeformat' => $timeformat,

            ];

        } else {
            $data = [
                'courseID' => $courseID,
                'coursename' => $coursename,
                'subcoursesid' => null,
                'subcourses' => null,
                'programname' => null,
                'day' => null,
                'timein' => null,
                'timeout' => null,
                'timeformat' => null,
            ];
        }
        $export = new coursesDataExport($data);
        $fileName = 'coursesDataExport.xlsx';

        return Excel::download($export, $fileName);
    }


    public function BlukUpload()
    {
        return view('backend/courses/BlukUpload');
    }

    public function BlukUploaddata(Request $request)
    {
        
        // Validate the uploaded file
        $request->validate([
            'attachment' => 'required|mimes:xlsx,xls|max:2048' // Adjust max file size if needed
        ]);

        // Get the uploaded file
        $file = $request->file('attachment');

        // Read the Excel file data
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);

        // Assuming the data is in the first sheet
        $worksheet = $spreadsheet->getActiveSheet();

        // Get the highest row and column indexes
     

        // Read the Excel file data
        // Read the Excel file data
        $data = Excel::toCollection(null, $file)[0]; // Assuming the data is in the first sheet

        // Remove the header row
        $data->shift();
        $rowIndex = 0;

        // Process each row of data
        foreach ($data as $row) {
            // Find or create a Course instance based on the Name
            $course = Courses::updateOrCreate([
                'name' => preg_replace('/[^a-zA-Z0-9]/', '', $row[1])
            ]);

            // Find the SubCourse instance based on the ID
            $subcourse = SubCourses::where('name', $row[3])->first();

            if ($subcourse) {
                // Update the existing SubCourse instance
                $subcourse->update(['courses_id' => $course->id]);
            } else {
                // Create a new SubCourse instance
                $subcourse = SubCourses::create([
                    'courses_id' => $course->id,
                    'name' => $row[3]
                ]);
            }

            // Find or create a Time instance based on the SubCourse ID, Program, Day, Time In, and Time Out
            $times = Time::updateOrCreate(
                [
                    'sub_courses_id' => $subcourse->id,
                    'program' => $worksheet->getCell('E' . ($rowIndex + 2))->getFormattedValue(),
                    'day' => $worksheet->getCell('F' . ($rowIndex + 2))->getFormattedValue()
                ],
                [
                    'time_in' => $worksheet->getCell('G' . ($rowIndex + 2))->getFormattedValue(),
                    'time_out' => $worksheet->getCell('H' . ($rowIndex + 2))->getFormattedValue()
                ]
            );

            $course->subCourses()->save($subcourse);
            $subcourse->times()->save($times);

            $rowIndex++;
        }
        


        return redirect()->route('courses');
    }



    public function fulldata()
    {

        $SubCourses = Courses::with('subCourses.times')->get();

        $response = [
            'data' => $SubCourses->map(function ($course) {
                return [
                    'courses' => [
                        'id' => $course->id,
                        'name' => $course->name,
                    ],
                    'sub_courses' => $course->subCourses->map(function ($subCourse) {
                        return [
                            'id' => $subCourse->id,
                            'courses_id' => $subCourse->courses_id,
                            'name' => $subCourse->name,
                            'times' => $subCourse->times->map(function ($time) {
                                return [
                                    'id' => $time->id,
                                    'sub_courses_id' => $time->sub_courses_id,
                                    'program' => $time->program,
                                    'day' => $time->day,
                                    'time_in' => $time->time_in,
                                    'time_out' => $time->time_out,
                                    'created_at' => $time->created_at,
                                    'updated_at' => $time->updated_at,
                                ];
                            }),
                        ];
                    }),
                ];
            }),
            'message' => 'Data retrieved successfully',
        ];

        return response()->json($response);

        // echo $SubCourses;
        exit;

    }

    public function ScheduleBy(Request $request, $id)
    {
        $Times = Time::where('sub_courses_id', $id)->get();
        // echo $time;
        // exit;
        return view('backend/courses/scheduleby')->with(compact('Times'));
    }

    public function index(Datatables $datatables)
    {
        // $a = Courses::get();
        // echo $a;
        // exit;
        $columns = [
            'id' => [
                'title' => 'No.',
                'orderable' => false,
                'searchable' => false,
                'render' => function () {
                    return 'function(data,type,fullData,meta){return meta.settings._iDisplayStart+meta.row+1;}';
                }
            ],
            'name',
            'created_at',
            'updated_at',
            'action' => ['orderable' => false, 'searchable' => false]
        ];

        if ($datatables->getRequest()->ajax()) {
            return $datatables->of(Courses::all())
                ->addColumn('action', function (Courses $data) {
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
                ->rawColumns(['action', 'intro'])
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

        return view('backend.courses.index', compact('html'));
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
            'role_play',
            'created_at',
            'updated_at',
            'action' => ['orderable' => false, 'searchable' => false]
        ];

        if ($datatables->getRequest()->ajax()) {
            return $datatables->of(User::where('role_play', 'teacher'))
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function add()
    {
        $data = new Courses();
        $data->form_action = $this->getRoute() . '.create';
        // Add page type here to indicate that the form.blade.php is in 'add' mode
        $data->page_type = 'add';
        $data->button_text = 'Add';

        if (Auth::user()->hasRole('administrator')) {
            return view('backend.courses.form', [
                'data' => $data,
                'role' => Role::orderBy('id')->pluck('display_name', 'id'),
            ]);
        }

        return view('backend.courses.form', [
            'data' => $data,
            'role' => Role::whereNotIn('id', [1, 2])->orderBy('id')->pluck('display_name', 'id'),
        ]);
    }

    /**
     * Get named route depends on which user is logged in
     *
     * @return String
     */
    private function getRoute()
    {
        return 'courses';
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
        $validator = Validator::make(\request()->all(), [
            'name' => 'required|max:50|unique:courses',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }

        Courses::create([
            'name' => request()->get('name'),
        ]);

        return redirect('courses')->with('success', Config::get('const.SUCCESS_CREATE_MESSAGE'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Courses::find($id);
        $data->form_action = $this->getRoute() . '.update';
        // Add page type here to indicate that the form.blade.php is in 'edit' mode
        $data->page_type = 'edit';
        $data->button_text = 'Edit';

        if (Auth::user()->hasRole('administrator')) {
            return view('backend.courses.form', [
                'data' => $data,
                'role' => Role::orderBy('id')->pluck('display_name', 'id'),
            ]);
        }

        return view('backend.courses.form', [
            'data' => $data,
            'role' => Role::whereNotIn('id', [1, 2])->orderBy('id')->pluck('display_name', 'id'),
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

        $currentData = Courses::find($request->get('id'));

        $validator = Validator::make(\request()->all(), [
            'name' => 'required|max:50|unique:courses,name,' . $currentData->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }
        $currentData->update([
            'name' => request()->get('name'),
        ]);
        return redirect('courses')->with('success', Config::get('const.SUCCESS_UPDATE_MESSAGE'));
        ;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $Courses = Courses::find($id);
        $Courses->delete();
        return redirect('courses')->with('success', Config::get('const.SUCCESS_DELETE_MESSAGE'));
        ;
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