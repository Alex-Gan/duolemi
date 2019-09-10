<?php
/**
 * 体验课程
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExperienceCourse extends Model
{
    protected $primaryKey = 'id';

    protected $table = 'experience_course';

    protected $guarded = ['id'];
}