<?php

namespace App\Http\Controllers\Backend\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Models\Attendance;
use App\Models\SubCourses;
use App\Models\Courses;
use Auth;
use Config;
use DateTime;
use Yajra\DataTables\Contracts\Formatter;

class AttendanceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $format;

    public function __construct($format = 'Y-m-d h:i a')
    {
        $this->middleware('auth');
        $this->format = $format;
    }

    /**
     * Show the application dashboard.
     * More info DataTables : https://yajrabox.com/docs/laravel-datatables/master
     *
     * @param Datatables $datatables
     * @param Request $request
     * @return Application|Factory|Response|View
     * @throws \Exception
     */
    public function index(Datatables $datatables, Request $request)
    {

        $columns = [
            'name' => ['name' => 'user.name'],
            'date',
            'in_time',
            'out_time',
            'work_hour' => ['title' => 'Total Hour'],
            // 'over_time',
            'late_time',
            'early_out_time',
            // 'in_location_id' => ['name' => 'areaIn.name', 'title' => 'In Location'],
            // 'out_location_id' => ['name' => 'areaOut.name', 'title' => 'Out Location']
        ];

        $from = date($request->dateFrom);
        $to = date($request->dateTo);

        if ($datatables->getRequest()->ajax()) {
            $query = Attendance::with('user', 'areaIn', 'areaOut')
                ->select('attendances.*');

            if ($from && $to) {
                $query = $query->whereBetween('date', [$from, $to]);
            }

            // worker
            if (Auth::user()->hasRole('staff') || Auth::user()->hasRole('admin')) {
                $query = $query->where('worker_id', Auth::user()->id);
            }

            return $datatables->of($query)
                ->addColumn('name', function (Attendance $data) {
                    return $data->user->name;
                })
                ->addColumn('in_location_id', function (Attendance $data) {
                    return $data->in_location_id == null ? '' : $data->areaIn->name;
                })
                ->addColumn('out_location_id', function (Attendance $data) {
                    return $data->out_location_id == null ? '' : $data->areaOut->name;
                })
                ->rawColumns(['name', 'out_location_id', 'in_location_id'])
                ->toJson();
        }

        $columnsArrExPr = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        $html = $datatables->getHtmlBuilder()
            ->columns($columns)
            ->minifiedAjax('', $this->scriptMinifiedJs())
            ->parameters([
                'order' => [[1, 'desc'], [2, 'desc']],
                'responsive' => true,
                'autoWidth' => false,
                'lengthMenu' => [
                    [10, 25, 50, -1],
                    ['10 rows', '25 rows', '50 rows', 'Show all']
                ],
                'dom' => 'Bfrtip',
                'buttons' => $this->buttonDatatables($columnsArrExPr),
            ]);
        $courses = Courses::get();
        $subcourses = SubCourses::get();
        return view('backend.attendances.index', compact('html', 'courses', 'subcourses'));
    }

    /**
     * Fungtion show button for export or print.
     *
     * @param $columnsArrExPr
     * @return array[]
     */
    public function buttonDatatables($columnsArrExPr)
    {
        return [
            [
                'pageLength'
            ],
            [
                'extend' => 'csvHtml5',
                'exportOptions' => [
                    'columns' => $columnsArrExPr
                ]
            ],
            [
                'extend' => 'pdfHtml5',
                'exportOptions' => [
                    'columns' => $columnsArrExPr
                ]
            ],
            [
                'extend' => 'excelHtml5',
                'exportOptions' => [
                    'columns' => $columnsArrExPr
                ]
            ],
            [
                'extend' => 'print',
                'exportOptions' => [
                    'columns' => $columnsArrExPr
                ]
            ],
        ];
    }

    /**
     * Get script for the date range.
     *
     * @return string
     */
    public function scriptMinifiedJs()
    {
        // Script to minified the ajax
        return <<<CDATA
            var formData = $("#date_filter").find("input").serializeArray();
            $.each(formData, function(i, obj){
                data[obj.name] = obj.value;
            });
CDATA;
    }

    public function student(Datatables $datatables, Request $request)
    {
        $columns = [
            'name' => ['name' => 'user.name'],
            'date',
            'in_time',
            'out_time',
            'work_hour',
            'over_time',
            'late_time',
            'early_out_time',
            'in_location_id' => ['name' => 'areaIn.name', 'title' => 'In Location'],
            'out_location_id' => ['name' => 'areaOut.name', 'title' => 'Out Location']
        ];

        $from = date($request->dateFrom);
        $to = date($request->dateTo);

        if ($datatables->getRequest()->ajax()) {
            $query = Attendance::with('user', 'areaIn', 'areaOut')
                ->select('attendances.*')->join('users', 'users.id', '=', 'attendances.worker_id')
                ->where('users.editor', 'student');;

            if ($from && $to) {
                $query = $query->whereBetween('date', [$from, $to]);
            }

            // worker
            if (Auth::user()->hasRole('staff') || Auth::user()->hasRole('admin')) {
                $query = $query->where('worker_id', Auth::user()->id);
            }

            return $datatables->of($query)
                ->addColumn('name', function (Attendance $data) {
                    return $data->user->name;
                })
                ->addColumn('in_location_id', function (Attendance $data) {
                    return $data->in_location_id == null ? '' : $data->areaIn->name;
                })
                ->addColumn('out_location_id', function (Attendance $data) {
                    return $data->out_location_id == null ? '' : $data->areaOut->name;
                })
                ->rawColumns(['name', 'out_location_id', 'in_location_id'])
                ->toJson();
        }

        $columnsArrExPr = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        $html = $datatables->getHtmlBuilder()
            ->columns($columns)
            ->minifiedAjax('', $this->scriptMinifiedJs())
            ->parameters([
                'order' => [[1, 'desc'], [2, 'desc']],
                'responsive' => true,
                'autoWidth' => false,
                'lengthMenu' => [
                    [10, 25, 50, -1],
                    ['10 rows', '25 rows', '50 rows', 'Show all']
                ],
                'dom' => 'Bfrtip',
                'buttons' => $this->buttonDatatables($columnsArrExPr),
            ]);

        return view('backend.attendances.index', compact('html'));
    }

    public function teacher(Datatables $datatables, Request $request)
    {
        $columns = [
            'name' => ['name' => 'user.name'],
            'date',
            'in_time',
            'out_time',
            'work_hour',
            'over_time',
            'late_time',
            'early_out_time',
            'in_location_id' => ['name' => 'areaIn.name', 'title' => 'In Location'],
            'out_location_id' => ['name' => 'areaOut.name', 'title' => 'Out Location']
        ];

        $from = date($request->dateFrom);
        $to = date($request->dateTo);

        if ($datatables->getRequest()->ajax()) {
            $query = Attendance::with('user', 'areaIn', 'areaOut')
                ->select('attendances.*')->join('users', 'users.id', '=', 'attendances.worker_id')
                ->where('users.editor', 'teacher');;

            if ($from && $to) {
                $query = $query->whereBetween('date', [$from, $to]);
            }

            // worker
            if (Auth::user()->hasRole('staff') || Auth::user()->hasRole('admin')) {
                $query = $query->where('worker_id', Auth::user()->id);
            }

            return $datatables->of($query)
                ->addColumn('name', function (Attendance $data) {
                    return $data->user->name;
                })
                ->addColumn('in_location_id', function (Attendance $data) {
                    return $data->in_location_id == null ? '' : $data->areaIn->name;
                })
                ->addColumn('out_location_id', function (Attendance $data) {
                    return $data->out_location_id == null ? '' : $data->areaOut->name;
                })
                ->rawColumns(['name', 'out_location_id', 'in_location_id'])
                ->toJson();
        }

        $columnsArrExPr = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        $html = $datatables->getHtmlBuilder()
            ->columns($columns)
            ->minifiedAjax('', $this->scriptMinifiedJs())
            ->parameters([
                'order' => [[1, 'desc'], [2, 'desc']],
                'responsive' => true,
                'autoWidth' => false,
                'lengthMenu' => [
                    [10, 25, 50, -1],
                    ['10 rows', '25 rows', '50 rows', 'Show all']
                ],
                'dom' => 'Bfrtip',
                'buttons' => $this->buttonDatatables($columnsArrExPr),
            ]);

        return view('backend.attendances.index', compact('html'));
    }
}
