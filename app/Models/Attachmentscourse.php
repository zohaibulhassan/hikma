<?php

namespace App\Models;

use App\Models\Base\Attachmentscourse as Attachmentscourse;

class Area extends Attachmentscourse
{
    protected $fillable = [
        'courseid',
        'link',
        'name',
        'uploaddate',
    ];
}
