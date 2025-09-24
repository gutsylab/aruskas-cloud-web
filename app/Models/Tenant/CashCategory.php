<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashCategory extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'description',

        'created_by_id', 
        'updated_by_id', 
        'deleted_by_id',
    ];

    /**
     * Get the user who created this category.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * Get the user who last updated this category.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    /**
     * Get the user who deleted this category.
     */
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by_id');
    }

    /**
     * Scope for income categories.
     */
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    /**
     * Scope for expense categories.
     */
    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public static function types(){
        return [
            'income' => 'Pemasukkan',
            'expense' => 'Pengeluaran',
        ];
    }
}
