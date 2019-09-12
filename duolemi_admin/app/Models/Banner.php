<?php
/**
 * 轮播图
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $primaryKey = 'id';

    protected $table = 'banner';

    protected $guarded = ['id'];
}