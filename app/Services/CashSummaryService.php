<?php

namespace App\Services;

use App\Models\Tenant\Account;
use App\Models\Tenant\JournalLine;
use Illuminate\Support\Facades\DB;

class CashSummaryService
{
    public static function generateAsOf(string $endDate)
    {
        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

        // check if dates are valid
        if (!strtotime($endDate)) {
            if (!strtotime($endDate)) {
                $endDate = date("Y-m-t");
            }
        }

        // only cash accounts
        $accounts = Account::where('is_cash', '=', 1)
            ->orderBy('code', 'asc')
            ->get();

        $accountSummaries = JournalLine::select(
            [
                'j.id',
                'j.date',
                'j.code as journal_code',
                'a.id as account_id',
                'a.code as account_code',
                'a.name as account_name',
                DB::raw('SUM(jl.debit) as debit'),
                DB::raw('SUM(jl.credit) as credit'),
            ]
        )
            ->from('journal_lines as jl')
            ->join('journals as j', 'j.id', '=', 'jl.journal_id')
            ->join('accounts as a', 'a.id', '=', 'jl.account_id')
            ->where('j.date', '<=', $endDate)
            ->where('j.status', '=', 'posted')
            ->whereIn('a.id', $accounts->pluck('id')->toArray())
            ->groupBy('a.id')
            ->get()->map(function ($item) {
                $item->debit = (float) $item->debit;
                $item->credit = (float) $item->credit;
                $item->ballance = $item->debit - $item->credit;
                return $item;
            })->groupBy('account_id');

        $finalAccounts = $accounts->map(function ($account) use ($accountSummaries) {
            $summary = $accountSummaries->get($account->id);
            if ($summary) {
                $summary = $summary->first();
                return (object) [
                    'account_id' => $account->id,
                    'account_code' => $account->code,
                    'account_name' => $account->name,
                    'debit' => $summary->debit,
                    'credit' => $summary->credit,
                    'ballance' => $summary->ballance,
                ];
            } else {
                return (object) [
                    'account_id' => $account->id,
                    'account_code' => $account->code,
                    'account_name' => $account->name,
                    'debit' => 0,
                    'credit' => 0,
                    'ballance' => 0,
                ];
            }
        });

        return [
            "as_of_date" => $endDate,
            "accounts" => $finalAccounts
        ];
    }
}
