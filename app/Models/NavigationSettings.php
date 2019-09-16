<?php
/**
 * 导航设置
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NavigationSettings extends Model
{
    protected $primaryKey = 'id';

    protected $table = 'navigation_settings';

    protected $guarded = ['id'];
}