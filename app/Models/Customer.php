<?php
/**
 * 我的客户关系表
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $primaryKey = 'id';

    protected $table = 'customer';

    protected $guarded = ['id'];
}