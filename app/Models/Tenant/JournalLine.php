<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class JournalLine extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'journal_id',
        'account_id',

        'description',

        'debit',
        'credit',

        'created_by',
        'updated_by',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function journal()
    {
        return $this->belongsTo(Journal::class, 'journal_id');
    }
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
