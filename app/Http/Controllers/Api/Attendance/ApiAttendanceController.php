<?php

namespace App\Http\Controllers\Api\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Attendance;
use App\Models\Attendance_sec;
use App\Models\Location;
use App\Models\Setting;
use App\Models\Courses;
use App\Models\User;
use App\Models\CUser;
use App\Models\Time;
use App\Models\Base\Attachmentscourse;
use App\Models\Base\Subcourse as AttachmentSubCourse;
use Illuminate\Http\Request;
use Response;
use Carbon\Carbon;
use Config;

class ApiAttendanceController extends Controller
{
    /**
     * Store data attendance to DB
     * @param Request $request
     * @return \Illuminate\Http\Response
     */

    public function GetUser()
    {
        // $a = password_hash("thepassword", PASSWORD_BCRYPT);
        // echo $a;
        // exit;
        $user = CUser::get();
        echo $user;
    }

    public function UserPassChange(Request $request)
    {
        $email = $request->email;
        $password = $request->password;

        $users = CUser::where('email', $email)->get();


        foreach ($users as $user) {
            $user->plain_password = $password;
            $user->password = bcrypt($password);
            $user->save();
        }

        return response()->json([
            'message' => 'Your password has been changed'
        ]);
    }

    public function CT()
    {
        $t = Time::get();
        $a = $t[0]['time_in'];
        $time = \DateTime::createFromFormat('H:i:s', $a);

        var_dump($time);
        exit;
        $t = Time::get();
        $a = $t[0]['time_in'];
        // echo $a;
        var_dump($a);
    }

    public function GetCourses()
    {
        $Courses = Courses::get();
        echo $Courses;
    }

    // public function GetCourseBySubByTime(){

    // $ID = 56;
    // $user = User::where('id',$ID)->get();
    // echo $user[0]['id'];
    // exit;
    // $SubCourses = Courses::with('subCourses.times')->get();

    // $SubCourses =    $SubCourses->map(function ($course) {
    //         return [
    //             'courses' => [
    //                 'id' => $course->id,
    //                 'name' => $course->name,
    //             ],
    //             'sub_courses' => $course->subCourses->map(function ($subCourse) {
    //                 return [
    //                     'id' => $subCourse->id,
    //                     'courses_id' => $subCourse->courses_id,
    //                     'name' => $subCourse->name,
    //                     'times' => $subCourse->times->map(function ($time) {
    //                         return [
    //                             'id' => $time->id,
    //                             'sub_courses_id' => $time->sub_courses_id,
    //                             'program' => $time->program,
    //                             'day' => $time->day,
    //                             'time_in' => $time->time_in,
    //                             'time_out' => $time->time_out,
    //                             'created_at' => $time->created_at,
    //                             'updated_at' => $time->updated_at,
    //                         ];
    //                     }),
    //                 ];
    //             }),
    //         ];
    //     });

    // // return response()->json($response);

    // echo $SubCourses;
    // exit;

    // }

    public function GetCourseBySubByTime()
    {
        $email = request()->get('email');
        $users = User::where('email', $email)->get();

        $coursesId = $users->pluck('courses_id')->unique()->toArray();

        $subCourses = Courses::whereIn('id', $coursesId)
            ->with(['subCourses' => function ($query) use ($users) {
                $subCoursesId = $users->pluck('sub_courses_id')->unique()->toArray();
                $query->whereIn('id', $subCoursesId)->with('times');
            }])
            ->get();

        $response = [];

        foreach ($subCourses as $course) {
            $courseData = [
                'course' => [
                    'id' => $course->id,
                    'name' => $course->name,
                    'attachments' => [], // Initialize the attachments array
                ],
                'sub_courses' => [],
            ];

            // Retrieve attachments from attachmentscourse table based on course_id
            $courseAttachments = AttachmentsCourse::where('courseid', $course->id)->get();

            foreach ($courseAttachments as $attachment) {
                $courseData['course']['attachments'][] = [
                    'id' => $attachment->id,
                    'course_id' => $attachment->courseid,
                    'link' => $attachment->link,
                    'name' => $attachment->name,
                    'uploaddate' => $attachment->uploaddate,
                ];
            }

            foreach ($course->subCourses as $subCourse) {
                $subCourseData = [
                    'id' => $subCourse->id,
                    'courses_id' => $subCourse->courses_id,
                    'name' => $subCourse->name,
                    'times' => [],
                    'attachments' => [], // Initialize the attachments array
                ];

                foreach ($subCourse->times as $time) {
                    $subCourseData['times'][] = [
                        'id' => $time->id,
                        'sub_courses_id' => $time->sub_courses_id,
                        'program' => $time->program,
                        'day' => $time->day,
                        'time_in' => $time->getFormattedTimeInAttribute(),
                        'time_in_apm' => $time->time_in_apm,
                        'time_out' => $time->getFormattedTimeOutAttribute(),
                        'time_out_apm' => $time->time_out_apm,
                        'created_at' => $time->created_at,
                        'updated_at' => $time->updated_at,
                    ];
                }

                // Retrieve attachments from attachmentssubcourse table based on sub_course_id
                $subCourseAttachments = AttachmentSubCourse::where('subcourseid', $subCourse->id)->get();

                foreach ($subCourseAttachments as $attachment) {
                    $subCourseData['attachments'][] = [
                        'id' => $attachment->id,
                        'sub_course_id' => $attachment->subcourseid,
                        'link' => $attachment->link,
                        'name' => $attachment->name,
                        'uploaddate' => $attachment->uploaddate,
                    ];
                }

                $courseData['sub_courses'][] = $subCourseData;
            }

            $response[] = $courseData;
        }

        if (empty($response)) {
            return response()->json(['message' => 'No attachments found']);
        }

        return response()->json($response);
    }



    public function apiSaveAttendance(Request $request)
    {
        // Get all request
        $new = $request->all();

        // Get data setting
        $getSetting = Setting::find(1);

        // Get user ID
        $ID = request()->get('id');
        $UserSubId = User::where('id', $ID)->get();
        $UserSubIdGet = $UserSubId[0]['sub_courses_id'];

        $Times = Time::where('sub_courses_id', $UserSubIdGet)->first();

        // Get user position
        $lat = $new['lat'];
        $longt = $new['longt'];

        $areaId = $new['area_id'];
        $q = $new['q'];
        $WorkerId = $new['worker_id'];

        $date = Carbon::now()->timezone($Times->timezone)->format('Y-m-d');

        // Check if user inside the area
        $getPoly = Location::whereIn('area_id', [$areaId])->get(['lat', 'longt']);
        if ($getPoly->count() == 0) {
            $data = [
                'message' => 'Location not found',
            ];
            return response()->json($data);
        }

        // Check if user is within the building area
        $isInsideBuilding = false;
        $margin = 0.0001; // Adjust this value to set the desired margin in latitude and longitude

        foreach ($getPoly as $corner) {
            $cornerLat = $corner['lat'];
            $cornerLong = $corner['longt'];

            // Check if the user's coordinates fall within the margin around the building corner
            if ($lat >= $cornerLat - $margin && $lat <= $cornerLat + $margin && $longt >= $cornerLong - $margin && $longt <= $cornerLong + $margin) {
                $isInsideBuilding = true;
                break;
            }
        }

        if (!$isInsideBuilding) {
            $data = [
                'message' => 'You are out of the building area',
            ];
            return response()->json($data);
        }

        // Check-in
        if ($q == 'in') {
            // Get data from request
            $in_time = new Carbon(Carbon::now()->timezone($Times->timezone)->format('H:i:s'));

            // Check if user already check-in
            $checkAlreadyCheckIn = Attendance::where('worker_id', $WorkerId)
                ->where('date', Carbon::now()->timezone($Times->timezone)->format('Y-m-d'))
                ->where('in_time', '<>', null)
                ->where('late_time', '<>', null)
                ->where('out_time', null)
                ->where('out_location_id', null)
                ->first();

            if ($checkAlreadyCheckIn) {
                $data = [
                    'message' => 'already check-in',
                ];
                return response()->json($data);
            }

            // Get late time
            $startHour = Carbon::createFromFormat('H:i:s', $Times->time_in);
            if (!$in_time->gt($startHour)) {
                $lateTime = "00:00:00";
            } else {
                $lateTime = $in_time->diff($startHour)->format('%H:%I:%S');
            }

            $location = Area::find($areaId)->name;
            // echo $location;
            // exit;
            // Save the data
            $save = new Attendance();
            $save->worker_id = $WorkerId;
            $save->date = $date;
            $save->in_location_id = $areaId;
            $save->in_time = $in_time;
            $save->late_time = $lateTime;

            $createNew = $save->save();

            // Saving
            if ($createNew) {
                $data = [
                    'message' => 'Check-in',
                    'date' => Carbon::parse($date)->format('Y-m-d'),
                    'time' => Carbon::parse($in_time)->format('H:i:s'),
                    'location' => $location,
                    'query' => 'Check-in',
                ];
                return response()->json($data);
            }

            $data = [
                'message' => 'Error! Something Went Wrong!',
            ];
            return response()->json($data);
        }

        // Check-out
        if ($q == 'out') {
            // Get data from request
            $out_time = new Carbon(Carbon::now()->timezone($Times->timezone)->format('H:i:s'));
            $getOutHour = new Carbon($Times->time_out);

            // Get data in_time from DB
            // To get data work hour
            $getInTime = Attendance::where('worker_id', $WorkerId)
                ->where('date', Carbon::now()->timezone($Times->timezone)->format('Y-m-d'))
                ->where('out_time', null)
                ->where('out_location_id', null)
                ->first();

            if (!$getInTime) {
                $data = [
                    'message' => 'check-in first',
                ];
                return response()->json($data);
            }

            $in_time = Carbon::createFromFormat('H:i:s', $getInTime->in_time);

            // Get data total working hour
            $getWorkHour = $out_time->diff($in_time)->format('%H:%I:%S');

            // Get over time
            if ($in_time->gt($getOutHour) || !$out_time->gt($getOutHour)) {
                $getOverTime = "00:00:00";
            } else {
                $getOverTime = $out_time->diff($getOutHour)->format('%H:%I:%S');
            }

            // Early out time
            if ($in_time->gt($getOutHour)) {
                $earlyOutTime = "00:00:00";
            } else {
                $earlyOutTime = $getOutHour->diff($out_time)->format('%H:%I:%S');
            }

            $location = Area::find($areaId)->name;
            // echo $location;
            // exit;

            // Update the data
            $getInTime->out_time = $out_time;
            $getInTime->over_time = $getOverTime;
            $getInTime->work_hour = $getWorkHour;
            $getInTime->early_out_time = $earlyOutTime;
            $getInTime->out_location_id = $areaId;

            $updateData = $getInTime->save();

            // Updating
            if ($updateData) {
                $data = [
                    'message' => 'Check-out',
                    'date' => Carbon::parse($date)->format('Y-m-d'),
                    'time' => Carbon::parse($out_time)->format('H:i:s'),
                    'location' => $location,
                    'query' => 'Check-Out',
                ];
                return response()->json($data);
            }
            $data = [
                'message' => 'Error! Something Went Wrong!',
            ];
            return response()->json($data);
        }
        $data = [
            'message' => 'Error! Wrong Command!',
        ];

        return response()->json($data);
    }

    // public function apiSaveAttendance(Request $request)
    // {
    //     // echo 'assss';
    //     // exit;
    //     // Get all request
    //     $new = $request->all();

    //     // Get data setting
    //     $getSetting = Setting::find(1);

    //     // Get data from request
    //     // $key = $new['key'];

    //     // Get user position
    //     $lat = $new['lat'];
    //     $lateightCharsorg = substr($lat, 0, 7);
    //     // echo $lateightCharsorg;
    //     // exit;
    //     $longt = $new['longt'];
    //     $longteightCharsorg = substr($longt, 0, 7);
    //     // echo $longteightCharsorg;
    //     // exit;

    //     $areaId = $new['area_id'];
    //     $q = $new['q'];
    //     $WorkerId = $new['worker_id'];

    //     $date = Carbon::now()->timezone($getSetting->timezone)->format('Y-m-d');

    //             // Check if user inside the area
    //             $getPoly = Location::whereIn('area_id', [$areaId])->get(['lat', 'longt']);
    //             $jsonData = $getPoly;
    //             $data = json_decode($jsonData, true); 
    //             $latcs = $data[0]['lat']; 
    //             $lateightChars = substr($latcs, 0, 7);

    //             $longtcs = $data[0]['longt']; 
    //             $longteightChars = substr($longtcs, 0, 7);
    //             // echo $longtsevenChars;
    //             // echo $getPoly;
    //             // exit;
    //             // echo $lat.' '.$longt.' '.$areaId.' '.$q.' '.$WorkerId.' '.$getPoly;
    //             // exit;
    //             if ($getPoly->count() == 0) {
    //                 $data = [
    //                     'message' => 'location not found',
    //                 ];
    //                 return response()->json($data);
    //             }
    //             // $isInside = $this->isInsidePolygon($lat, $longt, $getPoly);
    //             if ($lateightCharsorg != $lateightChars && $longteightCharsorg != $longteightChars) {
    //                 $data = [
    //                     'message' => 'You are out of radius',
    //                 ];
    //                 return response()->json($data);
    //             }

    //             // Check-in
    //             if ($q == 'in') {
    //                 // Get data from request
    //                 $in_time = new Carbon(Carbon::now()->timezone($getSetting->timezone)->format('H:i:s'));

    //                 // Check if user already check-in
    //                 $checkAlreadyCheckIn = Attendance::where('worker_id', $WorkerId)
    //                     ->where('date', Carbon::now()->timezone($getSetting->timezone)->format('Y-m-d'))
    //                     ->where('in_time', '<>', null)
    //                     ->where('late_time', '<>', null)
    //                     ->where('out_time', null)
    //                     ->where('out_location_id', null)
    //                     ->first();

    //                 if ($checkAlreadyCheckIn) {
    //                     $data = [
    //                         'message' => 'already check-in',
    //                     ];
    //                     return response()->json($data);
    //                 }

    //                 // Get late time
    //                 $startHour = Carbon::createFromFormat('H:i:s', $getSetting->start_time);
    //                 if (!$in_time->gt($startHour)) {
    //                     $lateTime = "00:00:00";
    //                 } else {
    //                     $lateTime = $in_time->diff($startHour)->format('%H:%I:%S');
    //                 }

    //                 $location = Area::find($areaId)->name;
    //                 // echo $location;
    //                 // exit;
    //                 // Save the data
    //                 $save = new Attendance();
    //                 $save->worker_id = $WorkerId;
    //                 $save->date = $date;
    //                 $save->in_location_id = $areaId;
    //                 $save->in_time = $in_time;
    //                 $save->late_time = $lateTime;

    //                 $createNew = $save->save();

    //                 // Saving
    //                 if ($createNew) {
    //                     $data = [
    //                         'message' => 'Check-in',
    //                         'date' => Carbon::parse($date)->format('Y-m-d'),
    //                         'time' => Carbon::parse($in_time)->format('H:i:s'),
    //                         'location' => $location,
    //                         'query' => 'Check-in',
    //                     ];
    //                     return response()->json($data);
    //                 }

    //                 $data = [
    //                     'message' => 'Error! Something Went Wrong!',
    //                 ];
    //                 return response()->json($data);
    //             }

    //             // Check-out
    //             if ($q == 'out') {
    //                 // Get data from request
    //                 $out_time = new Carbon(Carbon::now()->timezone($getSetting->timezone)->format('H:i:s'));
    //                 $getOutHour = new Carbon($getSetting->out_time);

    //                 // Get data in_time from DB
    //                 // To get data work hour
    //                 $getInTime = Attendance::where('worker_id', $WorkerId)
    //                     ->where('date', Carbon::now()->timezone($getSetting->timezone)->format('Y-m-d'))
    //                     ->where('out_time', null)
    //                     ->where('out_location_id', null)
    //                     ->first();

    //                 if (!$getInTime) {
    //                     $data = [
    //                         'message' => 'check-in first',
    //                     ];
    //                     return response()->json($data);
    //                 }

    //                 $in_time = Carbon::createFromFormat('H:i:s', $getInTime->in_time);

    //                 // Get data total working hour
    //                 $getWorkHour = $out_time->diff($in_time)->format('%H:%I:%S');

    //                 // Get over time
    //                 if ($in_time->gt($getOutHour) || !$out_time->gt($getOutHour)) {
    //                     $getOverTime = "00:00:00";
    //                 } else {
    //                     $getOverTime = $out_time->diff($getOutHour)->format('%H:%I:%S');
    //                 }

    //                 // Early out time
    //                 if ($in_time->gt($getOutHour)) {
    //                     $earlyOutTime = "00:00:00";
    //                 } else {
    //                     $earlyOutTime = $getOutHour->diff($out_time)->format('%H:%I:%S');
    //                 }

    //                 $location = Area::find($areaId)->name;
    //                 // echo $location;
    //                 // exit;

    //                 // Update the data
    //                 $getInTime->out_time = $out_time;
    //                 $getInTime->over_time = $getOverTime;
    //                 $getInTime->work_hour = $getWorkHour;
    //                 $getInTime->early_out_time = $earlyOutTime;
    //                 $getInTime->out_location_id = $areaId;

    //                 $updateData = $getInTime->save();

    //                 // Updating
    //                 if ($updateData) {
    //                     $data = [
    //                         'message' => 'Check-out',
    //                         'date' => Carbon::parse($date)->format('Y-m-d'),
    //                         'time' => Carbon::parse($out_time)->format('H:i:s'),
    //                         'location' => $location,
    //                         'query' => 'Check-Out',
    //                     ];
    //                     return response()->json($data);
    //                 }
    //                 $data = [
    //                     'message' => 'Error! Something Went Wrong!',
    //                 ];
    //                 return response()->json($data);
    //             }
    //             $data = [
    //                 'message' => 'Error! Wrong Command!',
    //             ];

    //     return response()->json($data);
    // }

    public function apiCheckoutAttendance(Request $request)
    {
        // echo 'apiCheckoutAttendance';
        // exit;
        // Get all request
        $new = $request->all();

        // Get data setting
        $getSetting = Setting::find(1);

        // Get data from request
        // $key = $new['key'];

        // Get user position
        $lat = $new['lat'];
        $lateightCharsorg = substr($lat, 0, 0);
        // echo $lateightCharsorg;
        // exit;
        $longt = $new['longt'];
        $longteightCharsorg = substr($longt, 0, 0);
        // echo $longteightCharsorg;
        // exit;

        $areaId = $new['area_id'];
        $q = $new['q'];
        $WorkerId = $new['worker_id'];

        $date = Carbon::now()->timezone($getSetting->timezone)->format('Y-m-d');

        // Check if user inside the area
        $getPoly = Location::whereIn('area_id', [$areaId])->get(['lat', 'longt']);
        $jsonData = $getPoly;
        $data = json_decode($jsonData, true);
        $latcs = $data[0]['lat'];
        $lateightChars = substr($latcs, 0, 0);

        $longtcs = $data[0]['longt'];
        $longteightChars = substr($longtcs, 0, 0);
        // echo $longtsevenChars;
        // echo $getPoly;
        // exit;
        // echo $lat.' '.$longt.' '.$areaId.' '.$q.' '.$WorkerId.' '.$getPoly;
        // exit;
        if ($getPoly->count() == 0) {
            $data = [
                'message' => 'location not found',
            ];
            return response()->json($data);
        }
        // $isInside = $this->isInsidePolygon($lat, $longt, $getPoly);
        if ($lateightCharsorg != $lateightChars && $longteightCharsorg != $longteightChars) {
            $data = [
                'message' => 'You are out of radius',
            ];
            return response()->json($data);
        }


        // Check-out
        if ($q == 'out') {
            // Get data from request
            $out_time = new Carbon(Carbon::now()->timezone($getSetting->timezone)->format('H:i:s'));
            $getOutHour = new Carbon($getSetting->out_time);

            // Get data in_time from DB
            // To get data work hour
            $getInTime = Attendance::where('worker_id', $WorkerId)
                ->where('date', Carbon::now()->timezone($getSetting->timezone)->format('Y-m-d'))
                ->where('out_time', null)
                ->where('out_location_id', null)
                ->first();

            if (!$getInTime) {
                $data = [
                    'message' => 'check-in first',
                ];
                return response()->json($data);
            }

            $in_time = Carbon::createFromFormat('H:i:s', $getInTime->in_time);

            // Get data total working hour
            $getWorkHour = $out_time->diff($in_time)->format('%H:%I:%S');

            // Get over time
            if ($in_time->gt($getOutHour) || !$out_time->gt($getOutHour)) {
                $getOverTime = "00:00:00";
            } else {
                $getOverTime = $out_time->diff($getOutHour)->format('%H:%I:%S');
            }

            // Early out time
            if ($in_time->gt($getOutHour)) {
                $earlyOutTime = "00:00:00";
            } else {
                $earlyOutTime = $getOutHour->diff($out_time)->format('%H:%I:%S');
            }

            $location = Area::find($areaId)->name;
            // echo $location;
            // exit;

            // Update the data
            $getInTime->out_time = $out_time;
            $getInTime->over_time = $getOverTime;
            $getInTime->work_hour = $getWorkHour;
            $getInTime->early_out_time = $earlyOutTime;
            $getInTime->out_location_id = $areaId;

            $updateData = $getInTime->save();

            // Updating
            if ($updateData) {
                $data = [
                    'message' => 'Check-out',
                    'date' => Carbon::parse($date)->format('Y-m-d'),
                    'time' => Carbon::parse($out_time)->format('H:i:s'),
                    'location' => $location,
                    'query' => 'Check-Out',
                ];
                return response()->json($data);
            }
            $data = [
                'message' => 'Error! Something Went Wrong!',
            ];
            return response()->json($data);
        }
        $data = [
            'message' => 'Error! Wrong Command!',
        ];

        return response()->json($data);
    }

    /**
     * Check if user inside the area
     * @param $x
     * @param $y
     * @param $polygon
     * @return \Illuminate\Http\Response
     */
    public function isInsidePolygon($x, $y, $polygon)
    {
        $inside = false;
        for ($i = 0, $j = count($polygon) - 1, $iMax = count($polygon); $i < $iMax; $j = $i++) {
            $xi = $polygon[$i]['lat'];
            $yi = $polygon[$i]['longt'];
            $xj = $polygon[$j]['lat'];
            $yj = $polygon[$j]['longt'];

            $intersect = (($yi > $y) != ($yj > $y))
                && ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi);
            if ($intersect) {
                $inside = !$inside;
            }
        }

        return $inside;
    }

    public function attendancescs()
    {
        $attendances = Attendance_sec::orderBy('id', 'DESC')
            ->join('users', 'attendances.worker_id', '=', 'users.id')
            ->join('sub_courses', 'users.sub_courses_id', '=', 'sub_courses.id')
            ->select('attendances.*', 'sub_courses.name as subcoursename')
            ->get();

        echo $attendances;
    }
}
