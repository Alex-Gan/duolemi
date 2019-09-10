<?php
/**
 * 购买记录
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseHistory extends Model
{
    protected $primaryKey = 'id';

    protected $table = 'purchase_history';

    protected $guarded = ['id'];
}