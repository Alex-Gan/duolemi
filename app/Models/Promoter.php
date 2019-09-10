<?php
/**
 * 推广员
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promoter extends Model
{
    protected $primaryKey = 'id';

    protected $table = 'promoter';

    protected $guarded = ['id'];
}