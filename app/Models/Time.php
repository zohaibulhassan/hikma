<?php

namespace App\Models;

use App\Models\Base\Time as BaseTime;

class Time extends BaseTime
{
	protected $fillable = [
		'courses_id',
		'sub_courses_id',
		'program',
		'day',
		'time_in',
		'time_in_apm',
		'time_out',
		'time_out_apm',
		'timezone',
	];

	protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'date:Y-m-d',
        'updated_at' => 'date:Y-m-d',
    ];
    
    public function getFormattedTimeInAttribute()
    {
        $timeIn = $this->attributes['time_in'];
        return \DateTime::createFromFormat('H:i:s', $timeIn);
    }
    
    public function getFormattedTimeOutAttribute()
    {
        $timeOut = $this->attributes['time_out'];
        return \DateTime::createFromFormat('H:i:s', $timeOut);
    }

}
