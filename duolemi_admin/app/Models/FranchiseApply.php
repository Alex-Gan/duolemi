<?php
/**
 * 加盟申请
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FranchiseApply extends Model
{
    protected $primaryKey = 'id';

    protected $table = 'franchise_apply';

    protected $guarded = ['id'];
}