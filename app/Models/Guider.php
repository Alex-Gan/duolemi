<?php
/**
 * 推广员
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guider extends Model
{
    protected $primaryKey = 'id';

    protected $table = 'guider';

    protected $guarded = ['id'];
}