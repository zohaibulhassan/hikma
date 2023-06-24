<?php

namespace App\Models;

use App\Models\Base\Courses as BaseCourses;

class Courses extends BaseCourses
{   

    protected $fillable = [
        'name'
    ];

    public function subCourses()
    {
        return $this->hasMany(SubCourses::class, 'courses_id');
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
        // 'role' => 'integer',
    ];
}
