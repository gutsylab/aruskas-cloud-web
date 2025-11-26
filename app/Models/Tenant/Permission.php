<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'name',
        'label',
        //
        'module',
        'module_label',
        //
        'group',
        'group_label',
        //
        'sub_group',
        'sub_group_label',
        //
        'application',
        'application_label',
        //
        'description'
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
