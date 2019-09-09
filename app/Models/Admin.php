<?php
/**
 * 管理员
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $primaryKey = 'id';

    protected $table = 'admin';

    protected $guarded = ['id'];

    protected $hidden =['salt', 'updated_at'];

    public $timestamps = false;
}