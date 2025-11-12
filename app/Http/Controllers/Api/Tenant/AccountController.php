<?php

namespace App\Http\Controllers\Api\Tenant;

use Illuminate\Http\Request;
use App\Models\Tenant\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ApiController;

class AccountController extends ApiController
{
    /**
     * Show the account settings page.
     */
    public function index()
    {
        $tenant = request()->attributes->get('tenant');

        $accounts = Account::select([
            'id',
            'code',
            'name',
            'type',
            'cash_flow_type',
            'is_cash',
            DB::raw('
                    (
                        SELECT
                            COALESCE(SUM(jl.debit), 0)
                        FROM
                            journal_lines AS jl
                            JOIN journals AS j ON jl.journal_id = j.id
                        WHERE
                            jl.account_id = accounts.id
                            AND j.status = "posted"
                    ) AS total_debit
                '),
            DB::raw('
                    (
                        SELECT
                            COALESCE(SUM(jl.credit), 0)
                        FROM
                            journal_lines AS jl
                            JOIN journals AS j ON jl.journal_id = j.id
                        WHERE
                            jl.account_id = accounts.id
                            AND j.status = "posted"
                    ) AS total_credit
                '),
        ])
            ->orderBy('code')
            ->get()
            ->map(function ($account) {

                // get total debit from journal lines
                $totalDebit = $account->total_debit ?? 0;
                $totalCredit = $account->total_credit ?? 0;

                $balance = $totalDebit - $totalCredit;

                return [
                    'id' => $account->id,
                    'code' => $account->code,
                    'name' => $account->name,
                    'type' => $account->type,
                    'cash_flow_type' => $account->cash_flow_type,
                    'is_cash' => $account->is_cash,
                    'balance' => abs($balance),
                ];
            });
        return $this->responseSuccess(
            ["accounts" => $accounts]
        );
    }

    public function show($tenant_id, $id)
    {
        $tenant = request()->attributes->get('tenant');

        $account = Account::with(['createdBy', 'updatedBy'])->where('id', $id)->first();


        if (!$account) {
            return $this->responseError('Akun tidak ditemukan', 404);
        }

        return $this->responseSuccess([
            "account" => $account
        ]);
    }

    public function store(Request $request, $tenant_id)
    {
        $validatedData = $request->validate([
            'code' => 'required|string|max:255|unique:accounts,code',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255|in:asset,liability,equity,revenue,expense',
            'cash_flow_type' => 'required|string|max:255|in:in,out,both',
            'is_cash' => 'required|boolean',
        ], [
            'type.in' => 'Tipe akun yang dipilih tidak valid. Tipe yang diizinkan adalah asset, liability, equity, revenue, expense.',
            'cash_flow_type.in' => 'Tipe arus kas yang dipilih tidak valid. Tipe yang diizinkan adalah in, out, both.',
            'is_cash.boolean' => 'Field is cash harus true atau false.',
            'code.required' => 'Kode akun harus diisi.',
            'code.unique' => 'Kode akun sudah digunakan.',
            'name.required' => 'Nama akun harus diisi.',
        ]);

        $userId = Auth::user()->id;

        try {
            $account = Account::create($validatedData);

            return $this->responseSuccess([
                "account" => $account
            ], 'Berhasil menambahkan akun baru', 201);
        } catch (\Throwable $th) {
            Log::error('Error creating account: ' . $th->getMessage());
            return $this->responseError('Gagal menambahkan akun baru.', 500);
        }
    }

    public function update(Request $request, $tenant_id, $id)
    {
        $validatedData = $request->validate([
            'code' => 'required|string|max:255|unique:accounts,code,' . $id,
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255|in:asset,liability,equity,revenue,expense',
            'cash_flow_type' => 'required|string|max:255|in:in,out,both',
            'is_cash' => 'required|boolean',
        ], [
            'type.in' => 'Tipe akun yang dipilih tidak valid. Tipe yang diizinkan adalah asset, liability, equity, revenue, expense.',
            'cash_flow_type.in' => 'Tipe arus kas yang dipilih tidak valid. Tipe yang diizinkan adalah in, out, both.',
            'is_cash.boolean' => 'Field is cash harus true atau false.',
            'code.required' => 'Kode akun harus diisi.',
            'code.unique' => 'Kode akun sudah digunakan.',
            'name.required' => 'Nama akun harus diisi.',
        ]);

        $userId = Auth::user()->id;
        $account = Account::find($id);
        if (!$account) {
            return $this->responseError('Akun tidak ditemukan', 404);
        }

        try {
            $account->update($validatedData);

            return $this->responseSuccess([
                "account" => $account
            ], 'Berhasil memperbarui akun', 200);
        } catch (\Throwable $th) {
            Log::error('Error updating account: ' . $th->getMessage());
            return $this->responseError('Gagal memperbarui akun: ' . $th->getMessage(), 500);
        }
    }

    public function destroy($tenant_id, $id)
    {
        $account = Account::find($id);
        if (!$account) {
            return $this->responseError('Akun tidak ditemukan', 404);
        }

        try {
            $account->delete();

            return $this->responseSuccess([], 'Berhasil menghapus akun', 200);
        } catch (\Throwable $th) {
            Log::error('Error deleting account: ' . $th->getMessage());
            return $this->responseError('Gagal menghapus akun.', 500);
        }
    }
}
