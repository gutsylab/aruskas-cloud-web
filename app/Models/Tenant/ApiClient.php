<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class ApiClient extends Model
{
    protected $fillable = ['name','key_hash','ip_allowlist','rate_per_min','active'];
    protected $casts = ['ip_allowlist' => 'array'];
}
