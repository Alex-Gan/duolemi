<?php
/**
 * 提现
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    protected $primaryKey = 'id';

    protected $table = 'withdraw';

    protected $guarded = ['id'];
}