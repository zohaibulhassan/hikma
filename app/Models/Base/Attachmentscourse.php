<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Attachmentcourse;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Area
 * 
 * @property int $id
 * @property string $name
 * @property string $address
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Collection|Attachmentcourse[] $attendances
 *
 * @package App\Models\Base
 */
class Attachmentscourse extends Model
{
    protected $table = 'attachmentscourse';
}
