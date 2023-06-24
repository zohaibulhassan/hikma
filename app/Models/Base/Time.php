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
class Time extends Model
{
	protected $table = 'times';

	protected $casts = [
		'courses_id' => 'int',
		'sub_courses_id' => 'int',
		'program' => 'varchar',
		'day' => 'varchar',
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'worker_id');
	}

	public function subcourse()
	{
		return $this->belongsTo(SubCourse::class, 'sub_courses_id');
	}

}
