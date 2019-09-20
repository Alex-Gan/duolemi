<?php
/**
 * 微信支付日志
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WxPayLog extends Model
{
    protected $primaryKey = 'id';

    protected $table = 'wx_pay_log';

    protected $guarded = ['id'];
}