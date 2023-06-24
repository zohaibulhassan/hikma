<?php

namespace App\Http\Controllers\Backend\SubCourses;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Utils\Activity\SaveActivityLogController;
// use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;
use App\Models\Role;
use App\Models\User;
use App\Models\Courses;
use App\Models\SubCourses;
use Illuminate\Support\Facades\DB;
use Auth;
use Config;
use File;
use Validator;

class SubCoursesController extends Controller
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
        $SubCourses = SubCourses::join('courses', 'sub_courses.courses_id', '=', 'courses.id')
        ->get(['sub_courses.*', 'courses.name as courses_id']);
        // dd($subCourses);
        // exit;
        // $SubCourses = SubCourses::get();
        return view('backend/subcourses/card')->with(compact('SubCourses'));
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
            'courses_id',
            'name',
            'created_at',
            'updated_at',
            'action' => ['orderable' => false, 'searchable' => false]
        ];

        if ($datatables->getRequest()->ajax()) {
            return $datatables->of(SubCourses::all())
                ->addColumn('action', function (SubCourses $data) {
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

        return view('backend.subcourses.index', compact('html'));
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

        return view('backend.users.index', compact('html'));
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
            return view('backend.subcourses.form', [
                'data' => $data,
                'courses' => Courses::orderBy('id')->pluck('name', 'id'),
            ]);
        }

        return view('backend.subcourses.form', [
            'data' => $data,
            'courses' => Courses::whereNotIn('id', [1, 2])->orderBy('id')->pluck('name', 'id'),
        ]);
    }

    /**
     * Get named route depends on which user is logged in
     *
     * @return String
     */
    private function getRoute()
    {
        return 'subcourses';
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
            'name' => 'required|max:50|unique:sub_courses',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }

        SubCourses::create([
            'courses_id' => request()->get('courses_id'),
            'name' => request()->get('name'),
        ]);

        return redirect('sub-courses')->with('success', Config::get('const.SUCCESS_CREATE_MESSAGE'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = SubCourses::find($id);
        $data->form_action = $this->getRoute() . '.update';
        // Add page type here to indicate that the form.blade.php is in 'edit' mode
        $data->page_type = 'edit';
        $data->button_text = 'Edit';

        if (Auth::user()->hasRole('administrator')) {
            return view('backend.subcourses.form', [
                'data' => $data,
                'courses' => Courses::orderBy('id')->pluck('name', 'id'),
            ]);
        }

        return view('backend.subcourses.form', [
            'data' => $data,
            'courses' => courses::whereNotIn('id', [1, 2])->orderBy('id')->pluck('name', 'id'),
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

        $currentData = SubCourses::find($request->get('id'));

        $validator = Validator::make(\request()->all(), [
            'courses_id' => 'required|max:10|unique:sub_courses,name,'.$currentData->id,
            'name' => 'required|max:50|unique:sub_courses,name,'.$currentData->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }
        $currentData->update([
            'courses_id' => request()->get('courses_id'),
            'name' => request()->get('name'),
        ]);
        return redirect('sub-courses')->with('success', Config::get('const.SUCCESS_UPDATE_MESSAGE'));;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $Courses = SubCourses::find($id);
        $Courses->delete();
        return redirect('sub-courses')->with('success', Config::get('const.SUCCESS_DELETE_MESSAGE'));;
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

    /**
     * Function check email is exist or not.
     *
     * @param $data
     * @param $typeCheck
     * @return bool
     */
    public function checkDuplicate($data, $typeCheck)
    {
        if ($typeCheck == 'courses_id') {
            $isExists = SubCourses::where('courses_id', $data)->first();
        }

        if ($typeCheck == 'name') {
            $isExists = SubCourses::where('name', $data)->first();
        }

        if ($isExists) {
            return true;
        }

        return false;
    }
}
