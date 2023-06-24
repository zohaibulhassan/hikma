<?php

namespace App\Models;

use App\Models\Base\SubCourses as BaseSubCourses;

class SubCourses extends BaseSubCourses
{   

    protected $fillable = [
        'courses_id',
        'name'
    ];
    
    public function times()
    {
        return $this->hasMany(Time::class, 'sub_courses_id');
    }
    
    // Function get user image from database

    function adminlte_desc()
    {
        return 'Hi, Welcome!';
    }

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'date:Y-m-d',
        'updated_at' => 'date:Y-m-d',
        'courses' => 'integer',
    ];
}
