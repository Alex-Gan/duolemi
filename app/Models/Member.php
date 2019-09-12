<?php
/**
 * 会员
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $primaryKey = 'id';

    protected $table = 'member';

    protected $guarded = ['id'];
}