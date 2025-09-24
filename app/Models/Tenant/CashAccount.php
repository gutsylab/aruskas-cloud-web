<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashAccount extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'account_number',
        'name',
        'description',

        'created_by_id', 
        'updated_by_id', 
        'deleted_by_id',
    ];
}
