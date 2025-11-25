<?php

namespace App\Http\Controllers\Tenant;

use Exception;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Tenant\Account;
use Illuminate\Support\Facades\DB;
use App\Constants\TenantPermissions;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController;
use Yajra\DataTables\Facades\DataTables;

class CashAccountController extends BaseController
{
    private $title = 'Akun';

    private function groupMenu()
    {
        return [
            'application' => TenantPermissions::APPLICATION,
            'groupMenu' => TenantPermissions::GROUP_CASH,
            'subGroupMenu' => TenantPermissions::SUBGROUP_CASH_ACCOUNT,
            'moduleName' => TenantPermissions::MODULE_CASH_ACCOUNT,
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return $this->viewTenant(
            'cash.accounts.index',
            $this->title,
            $this->groupMenu(),
            [],
            [
                ['Kas' => ''],
                ['Akun' => 'javascript:void(0)'],
            ]
        );
    }

    public function dataTable()
    {
        $status = request()->get('status', '');

        $accounts = Account::select([
            'id',
            'code',
            'name',
            'type',
            'cash_flow_type',
            'is_cash',
            'deleted_at',
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
            ->where('is_cash', true)
            ->withTrashed()
            ->when(!empty($status), function ($query) use ($status) {
                return $query->where(function ($q) use ($status) {
                    if ($status == 'active') {
                        $q->whereNull('deleted_at');
                    } elseif ($status == 'archived') {
                        $q->whereNotNull('deleted_at');
                    }
                });
            });

        return DataTables::of($accounts)
            ->addIndexColumn()
            ->editColumn('code', function ($row) {
                // if (!userCan('view', User::class)) return $row->email;
                return '<a href="' . route('cash-accounts.edit', ['tenant_id' => $row->tenant_id, 'cash_account' => $row->id]) . '" class="text-decoration-underline text-primary">' . $row->code . '</a>';
            })
            ->editColumn('type', function ($row) {
                return ucfirst($row->type);
            })
            ->addColumn('balance', function ($row) {
                $totalDebit = $row->total_debit ?? 0;
                $totalCredit = $row->total_credit ?? 0;

                $balance = $totalDebit - $totalCredit;
                return convertCurrency($balance, true, 2);
            })
            ->addColumn('status', function ($row) {
                if ($row->deleted_at) {
                    return '<span class="badge bg-danger">Arsip</span>';
                } else {
                    return '<span class="badge bg-success">Aktif</span>';
                }
            })
            ->filterColumn('balance', function ($query, $keyword) {})
            ->rawColumns([
                'code',
                'status'
            ])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($tenant_id, string $id)
    {
        $account = Account::withTrashed()->with(['journalLines'])->find($id);
        //

        $title = $id == 0 ? 'Tambah ' . $this->title : 'Ubah ' . $this->title;

        return $this->viewTenant(
            'cash.accounts.edit',
            $title,
            $this->groupMenu(),
            compact('account'),
            [
                ['Kas' => ''],
                ['Akun' => route('cash-accounts.index')],
                [$title => 'javascript:void(0)'],
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $tenant_id,  string $id)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
        ], [
            'code.required' => 'Kode akun wajib diisi.',
            'code.string' => 'Kode akun harus berupa teks.',
            'code.max' => 'Kode akun maksimal :max karakter.',
            'name.required' => 'Nama akun wajib diisi.',
            'name.string' => 'Nama akun harus berupa teks.',
            'name.max' => 'Nama akun maksimal :max karakter.',
        ]);

        $accounts = Account::withTrashed()->where('id', '=', $id)->get();

        $account = null;
        if ($accounts->count() > 0) {
            $account = $accounts->first();
        }

        // ** check duplicate code
        $exists = Account::withTrashed()->where('code', '=', $validated['code'])
            ->where('id', '!=', $id)->first();
        if ($exists) {
            if ($account) {
                if ($exists->id != $account->id) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Kode akun sudah digunakan. Silakan gunakan kode lain.');
                }
            }
        }

        if ($account) {
            $account->code = $validated['code'];
            $account->name = $validated['name'];
            $account->save();

            return redirect()->route('cash-accounts.edit', ['tenant_id' => $request->tenant_id, 'cash_account' => $account->id])
                ->with('success', 'Akun berhasil diperbarui.');
        } else {
            $account = Account::create([
                'code' => $validated['code'],
                'name' => $validated['name'],
                'type' => 'asset',
                'cash_flow_type' => 'both',
                'is_cash' => true,
            ]);

            if ($account) {
                return redirect()->route('cash-accounts.edit', ['tenant_id' => $request->tenant_id, 'cash_account' => $account->id])
                    ->with('success', 'Akun berhasil dibuat.');
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal menyimpan akun. Silakan coba lagi.');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($tenant_id, string $id)
    {
        $account = Account::withTrashed()->with(['journalLines'])->find($id);
        if ($account) {
            if ($account->journalLines()->count() > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Akun tidak dapat dihapus karena sudah memiliki transaksi di arus kas.'
                ], 200);
            }

            try {
                $account->forceDelete();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Akun berhasil dihapus permanen.'
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal menghapus akun: ' . $e->getMessage()
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Akun tidak ditemukan.'
            ], 404);
        }
    }

    public function archive($tenant_id, string $id)
    {
        $account = Account::find($id);
        if ($account) {
            $account->delete();
            return redirect()->back()
                ->with('success', 'Akun berhasil diarsipkan.');
        } else {
            return redirect()->back()
                ->with('error', 'Akun tidak ditemukan.');
        }
    }

    public function active($tenant_id, string $id)
    {
        $account = Account::withTrashed()->find($id);
        if ($account) {
            $account->restore();
            return redirect()->back()
                ->with('success', 'Akun berhasil diaktifkan.');
        } else {
            return redirect()->back()
                ->with('error', 'Akun tidak ditemukan.');
        }
    }
}
