<?php

namespace App\Services;

use App\Models\Tenant\Account;
use App\Models\Tenant\JournalLine;
use Illuminate\Support\Facades\DB;

class BankStatementService
{
    public static function generate(Account $account, string $startDate, string $endDate)
    {

        if (!$account) {
            throw new \Exception("Account is required");
        }
        $account_id = $account->id;

        // check if dates are valid
        if (!strtotime($startDate) || !strtotime($endDate)) {
            if (!strtotime($startDate)) {
                $startDate = date("Y-m-01");
            }
            if (!strtotime($endDate)) {
                $endDate = date("Y-m-t");
            }
        }

        $transactions = JournalLine::select(
            [
                'j.date',
                'j.code as journal_code',
                'a.code as account_code',
                'a.name as account_name',
                'j.description as jounral_description',
                'j.reference as journal_reference',
                'jl.debit',
                'jl.credit',
            ]
        )
            ->from('journal_lines as jl')
            ->join('journals as j', 'j.id', '=', 'jl.journal_id')
            ->join('accounts as a', 'a.id', '=', 'jl.account_id')
            ->whereBetween('j.date', [
                $startDate,
                $endDate
            ])
            ->when($account_id > 0, function ($query) use ($account_id) {
                return $query->where('jl.account_id', '=', $account_id);
            })
            ->where('j.status', '=', 'posted')
            ->orderBy('j.date', 'asc')
            ->orderBy('j.created_at', 'asc')
            ->get()->map(function ($item) {
                $item->debit = (float) $item->debit;
                $item->credit = (float) $item->credit;
                return $item;
            });

        // Hitung saldo awal sebelum periode
        $openingBalance = JournalLine::from('journal_lines as jl')
            ->where('jl.account_id', '=', $account_id)
            ->where('j.date', '<', $startDate)
            ->join('journals as j', 'j.id', '=', 'jl.journal_id')
            ->selectRaw('SUM(jl.debit - jl.credit) as balance')
            ->value('balance') ?? 0;

        $runningBalance = (float) ($openingBalance ?? 0);
        $transactions->transform(function ($t) use (&$runningBalance) {
            $runningBalance += ($t->debit - $t->credit);
            $t->running_balance = $runningBalance;
            return $t;
        });

        return [
            "account" => $account,
            "opening_balance" => (float) $openingBalance ?? 0,
            "transactions" => $transactions,
        ];
    }
}
