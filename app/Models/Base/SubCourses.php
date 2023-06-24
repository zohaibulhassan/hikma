<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Area;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Attendance
 *
 * @property int $id
 * @property int $worker_id
 * @property Carbon $date
 * @property Carbon $in_time
 * @property Carbon $out_time
 * @property Carbon $work_hour
 * @property Carbon $over_time
 * @property Carbon $late_time
 * @property Carbon $early_out_time
 * @property int $in_location_id
 * @property int $out_location_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Area $area
 * @property User $user
 *
 * @package App\Models\Base
 */
class SubCourses extends Model
{
	protected $table = 'sub_courses';

	protected $casts = [
		'courses_id' => 'int',
		'name' => 'varchar'
	];

	public function course()
	{
		return $this->belongsTo(Course::class, 'courses_id');
	}
}
