<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\SoftDeletes;

class Journal extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'code',
        'date',
        'description',

        'reference',

        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->select(['id', 'name', 'email']);
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function lines()
    {
        return $this->hasMany(JournalLine::class, 'journal_id');
    }

    public function scopeCashFlowIn($query)
    {
        // Filter journals that have at least one line whose related account has cash_flow_type = 'in'
        return $query->whereHas('lines.account', function ($q) {
            $q->where('cash_flow_type', '=', 'in');
        });
    }

    public function scopeCashFlowOut($query)
    {
        // Filter journals that have at least one line whose related account has cash_flow_type = 'out'
        return $query->whereHas('lines.account', function ($q) {
            $q->where('cash_flow_type', '=', 'out');
        });
    }
}
