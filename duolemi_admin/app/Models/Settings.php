<?php
/**
 * 设置
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $primaryKey = 'id';

    protected $table = 'settings';

    protected $guarded = ['id'];
}