<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailProvider extends Model
{
    protected $fillable = ['user_id','name','type','credentials'];
    protected $casts = ['credentials' => 'encrypted:array'];
}
