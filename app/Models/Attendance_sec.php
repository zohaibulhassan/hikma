<?php

namespace App\Models;

use App\Models\Base\Attendance as BaseAttendance;
use \Carbon\Carbon;

class Attendance_sec extends BaseAttendance
{
	protected $fillable = [
		'worker_id',
		'date',
		'in_time',
		'out_time',
		'work_hour',
		'over_time',
		'late_time',
		'early_out_time',
		'in_location_id',
		'out_location_id'
	];

    protected $dates = [
        'date'
    ];

    protected $casts = [
        'date'  => 'date:Y-m-d',
    ];
    
    public function getOutTimeAttribute($value)
    {
        if ($value) {
            return Carbon::createFromFormat('H:i:s', $value)->format('h:i a');
        }
    
        return null;
    }
    
    public function getInTimeAttribute($value)
    {
        if ($value) {
            return Carbon::createFromFormat('H:i:s', $value)->format('h:i a');
        }
        
        return null;
    }
}
