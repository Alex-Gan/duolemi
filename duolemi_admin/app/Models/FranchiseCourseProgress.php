<?php
/**
 * 加盟课进度明细
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FranchiseCourseProgress extends Model
{
    protected $primaryKey = 'id';

    protected $table = 'franchise_course_progress';

    protected $guarded = ['id'];

    public $timestamps = false;
}