<?php
/**
 * 体验课进度明细
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExperienceProgress extends Model
{
    protected $primaryKey = 'id';

    protected $table = 'experience_progress';

    protected $guarded = ['id'];
}