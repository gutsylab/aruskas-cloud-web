<?php

namespace App\Http\Controllers\Tenant;

use Exception;

use Illuminate\Http\Request;
use App\Models\Tenant\Account;
use Illuminate\Support\Facades\DB;
use App\Constants\TenantPermissions;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController;
use Yajra\DataTables\Facades\DataTables;

class CashCategoryController extends BaseController
{
    private $title = 'Kategori';

    private function groupMenu()
    {
        return [
            'application' => TenantPermissions::APPLICATION,
            'groupMenu' => TenantPermissions::GROUP_CASH,
            'subGroupMenu' => TenantPermissions::SUBGROUP_CASH_CATEGORY,
            'moduleName' => TenantPermissions::MODULE_CASH_CATEGORY,
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return $this->viewTenant(
            'cash.categories.index',
            $this->title,
            $this->groupMenu(),
            [],
            [
                ['Kas' => ''],
                ['Kategori' => 'javascript:void(0)'],
            ]
        );
    }

    public function dataTable()
    {
        $type = request()->get('type', 'in');
        $status = request()->get('status', '');

        $tenant = request()->attributes->get('tenant');

        $accounts = Account::select(
            'id',
            'code',
            'name',
            'type',
            'cash_flow_type',
            'sort',
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
        )->withTrashed()
            ->where('is_cash', '=', 0)
            ->when($type, function ($query) use ($type) {
                $sorts = [4];

                if ($type == 'out') $sorts = [6];
                return $query->where('cash_flow_type', '=', $type)->whereIn('sort', $sorts);
            })
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
            ->editColumn('name', function ($row) use ($tenant) {
                // if (!userCan('view', User::class)) return $row->email;
                return '<a href="' . route('cash-categories.edit', ['tenant_id' => $tenant->tenant_id, 'cash_category' => $row->id]) . '" class="text-decoration-underline text-primary">' . $row->name . '</a>';
            })
            ->addColumn('balance', function ($row) {
                $totalDebit = $row->total_debit ?? 0;
                $totalCredit = $row->total_credit ?? 0;

                $balance = $totalDebit - $totalCredit;
                return convertCurrency(abs($balance), true, 2);
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
                'name',
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
        $type = request()->query('type', 'in');
        $title = $id == 0 ? 'Tambah ' . $this->title : 'Ubah ' . $this->title;

        if ($type == 'in') {
            $title .= ' Masuk';
        } else {
            $title .= ' Keluar';
        }

        return $this->viewTenant(
            'cash.categories.edit',
            $title,
            $this->groupMenu(),
            compact('account'),
            [
                ['Kas' => ''],
                ['Kategori' => route('cash-categories.index')],
                [$title => 'javascript:void(0)'],
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $tenant_id, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:in,out',
        ], [
            'name.required' => 'Nama akun wajib diisi.',
            'name.string' => 'Nama akun harus berupa teks.',
            'name.max' => 'Nama akun maksimal :max karakter.',
            'type.required' => 'Tipe akun wajib diisi.',
            'type.string' => 'Tipe akun harus berupa teks.',
            'type.in' => 'Tipe akun tidak valid.',
        ]);

        $type = $validated['type'];

        $accounts = Account::withTrashed()->where('id', '=', $id)->get();

        $account = null;
        if ($accounts->count() > 0) {
            $account = $accounts->first();
        }

        if ($account) {
            $account->name = $validated['name'];
            $account->save();

            return redirect()->route('cash-categories.edit', ['tenant_id' => $request->tenant_id, 'cash_category' => $account->id])
                ->with('success', 'Kategori berhasil diperbarui.');
        } else {

            $sort = 4;
            $account_type = 'revenue';
            if ($type == 'out') {
                $sort = 6;
                $account_type = 'expense';
            }
            // get max code for code
            $max = Account::where('sort', '=', $sort)
                ->where('cash_flow_type', '=', $type)
                ->max(DB::raw('CAST(code AS UNSIGNED)'));
            if ($max) {
                $newCode = $max + 1;
            } else {
                $newCode = ($type == 'in') ? 4001 : 6001;
            }

            $account = Account::create([
                'code' => $newCode,
                'name' => $validated['name'],
                'type' => $account_type,
                'cash_flow_type' => $type,
                'is_cash' => false,
                'sort' => $sort,
            ]);

            if ($account) {
                return redirect()->route('cash-categories.edit', ['tenant_id' => $request->tenant_id, 'cash_category' => $account->id, 'type' => $type])
                    ->with('success', 'Kategori berhasil dibuat.');
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal menyimpan kategori. Silakan coba lagi.');
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
                    'message' => 'Kategori tidak dapat dihapus karena sudah memiliki transaksi di arus kas.'
                ], 200);
            }

            try {
                $account->forceDelete();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Kategori berhasil dihapus permanen.'
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal menghapus kategori: ' . $e->getMessage()
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori tidak ditemukan.'
            ], 404);
        }
    }

    public function archive($tenant_id, string $id)
    {
        $account = Account::find($id);
        if ($account) {
            $account->delete();
            return redirect()->back()
                ->with('success', 'Kategori berhasil diarsipkan.');
        } else {
            return redirect()->back()
                ->with('error', 'Kategori tidak ditemukan.');
        }
    }

    public function active($tenant_id, string $id)
    {
        $account = Account::withTrashed()->find($id);
        if ($account) {
            $account->restore();
            return redirect()->back()
                ->with('success', 'Kategori berhasil diaktifkan.');
        } else {
            return redirect()->back()
                ->with('error', 'Kategori tidak ditemukan.');
        }
    }
}
