<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class EmailProvider extends Model
{
    protected $fillable = ['user_id','name','type','credentials'];
    protected $casts = ['credentials' => 'encrypted:array'];
}
