<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'type',
        'cash_flow_type',
        'is_cash',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_cash' => 'boolean',
    ];

    protected static function booted()
    {
        static::updating(function ($account) {
            if ($account->isDirty('is_cash')) {
                $hasJournal = $account->journalLines()->exists();
                if ($hasJournal) {
                    throw new \Exception("Akun ini sudah digunakan di arus kas, tidak boleh ubah status is_cash.");
                }
            }
        });
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function journalLines()
    {
        return $this->hasMany(JournalLine::class, 'account_id');
    }
}
