<?php

namespace App\Models;

use App\Models\Base\SubCourse as SubCourse;

class SubCourses extends SubCourse
{
    protected $fillable = [
        'subcourseid',
        'link',
        'name',
        'uploaddate',
    ];
}
