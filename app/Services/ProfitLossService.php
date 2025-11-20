<?php

namespace App\Services;

use App\Models\Tenant\JournalLine;
use Illuminate\Support\Facades\DB;

class ProfitLossService
{
    public static function generate(string $startDate, string $endDate)
    {
        // check if dates are valid
        if (!strtotime($startDate) || !strtotime($endDate)) {
            if (!strtotime($startDate)) {
                $startDate = date("Y-m-01");
            }
            if (!strtotime($endDate)) {
                $endDate = date("Y-m-t");
            }
        }

        $revenues = DB::table('journal_lines as jl')
            ->select('acc.code', 'acc.name', DB::raw('SUM(jl.credit - jl.debit) as amount'))
            ->join('accounts as acc', 'acc.id', '=', 'jl.account_id')
            ->join('journals as j', 'j.id', '=', 'jl.journal_id')
            ->whereBetween('j.date', [$startDate, $endDate])
            ->where('acc.type', 'revenue')
            ->where('j.status', 'posted')
            ->groupBy('acc.code', 'acc.name')
            ->get()->map(function ($item) {
                $item->amount = (float) $item->amount;
                $item->amount_formatted = number_format($item->amount, 2, ',', '.');
                return $item;
            });

        $expenses = DB::table('journal_lines as jl')
            ->select('acc.code', 'acc.name', DB::raw('SUM(jl.debit - jl.credit) as amount'))
            ->join('accounts as acc', 'acc.id', '=', 'jl.account_id')
            ->join('journals as j', 'j.id', '=', 'jl.journal_id')
            ->whereBetween('j.date', [$startDate, $endDate])
            ->where('acc.type', 'expense')
            ->where('j.status', 'posted')
            ->groupBy('acc.code', 'acc.name')
            ->get()->map(function ($item) {
                $item->amount = (float) $item->amount;
                $item->amount_formatted = number_format($item->amount, 2, ',', '.');
                return $item;
            });

        $total_revenue = $revenues->sum('amount');
        $total_expense = $expenses->sum('amount');
        $net_profit = $total_revenue - $total_expense;
        $state = $net_profit >= 0 ? 'profit' : 'loss';

        return [
            'summary' => [
                'total_revenue' => $total_revenue,
                'total_expense' => $total_expense,
                'net_profit' => $net_profit,
                'state' => $state
            ],
            'revenues' => $revenues,
            'expenses' => $expenses,
        ];
    }
}
