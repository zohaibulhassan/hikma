<?php

namespace App\Http\Controllers\Backend\Policy;

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

class PolicyController extends Controller
{
    public function index(){
       
        // $Times = Time::get();
        // echo 'aaaa';
        // exit;
        return view('backend/policy/index');
    }
    
}
