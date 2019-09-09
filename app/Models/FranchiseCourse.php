<?php
/**
 * 加盟课
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FranchiseCourse extends Model
{
    protected $primaryKey = 'id';

    protected $table = 'franchise_course';

    protected $guarded = ['id'];
}