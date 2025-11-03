<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Sequence extends Model
{
    use BelongsToTenant;

    //

    protected $fillable = [
        'code',
        'name',
        'prefix',
        'pattern',
        'reset_period',
        'number',
        'year',
        'month',
        'day',
    ];
}
