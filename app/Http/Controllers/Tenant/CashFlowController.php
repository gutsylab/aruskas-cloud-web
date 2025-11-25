<?php

namespace App\Http\Controllers\Tenant;

use Exception;
use Carbon\Carbon;
use App\Models\Tenant\User;

use Illuminate\Http\Request;
use App\Models\Tenant\Account;
use App\Models\Tenant\Journal;
use Illuminate\Support\Facades\DB;
use App\Constants\TenantPermissions;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class CashFlowController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    private $title = 'Arus Kas';

    private function groupMenu()
    {
        return [
            'application' => TenantPermissions::APPLICATION,
            'groupMenu' => TenantPermissions::GROUP_CASH,
            'subGroupMenu' => TenantPermissions::SUBGROUP_CASH_FLOW,
            'moduleName' => TenantPermissions::MODULE_CASH_FLOW,
        ];
    }
    public function index()
    {
        //
        return $this->viewTenant(
            'cash.cash-flows.index',
            $this->title,
            $this->groupMenu(),
            [],
            [
                ['Kas' => ''],
                ['Arus Kas' => 'javascript:void(0)'],
            ]
        );
    }

    public function dataTable(Request $request)
    {
        $status = $request->get('status', '');
        $startDate = $request->get('start_date', '');
        $endDate = $request->get('end_date', '');

        $cashFlows = Journal::select([
            'id',
            'code',
            'date',
            'type',
            'description',
            'reference',
            'status',
            'posted_at',
        ])
            ->with(['lines.account'])
            ->withSum('lines', 'debit')
            ->when(!empty($status) && in_array($status, ['draft', 'posted']), function ($query) use ($status) {
                $query->where('status', '=', $status);
            })
            ->when(!empty($startDate) && !empty($endDate), function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            });

        return DataTables::of($cashFlows)
            ->addIndexColumn()
            ->editColumn('code', function ($row) {
                // if (!userCan('view', User::class)) return $row->email;
                return '<a href="' . route('cash-flows.show', $row) . '" class="text-decoration-underline text-primary">' . $row->code . '</a>';
            })
            ->editColumn('date', function ($row) {
                return Carbon::parse($row->date)->format('d M Y');
            })
            ->filterColumn('date', function ($query, $keyword) {
                $query->whereDate('date', 'like', "%$keyword%");
            })
            ->editColumn('status', function ($row) {
                $status = '<span class="badge bg-secondary">Draft</span>';
                switch ($row->status) {
                    case 'posted':
                        $status = '<span class="badge bg-primary">Diposting</span>';
                        break;
                    case 'draft':
                        $status = '<span class="badge bg-secondary">Draft</span>';
                        break;
                    default:
                        $status = '<span class="badge bg-dark">Tidak Diketahui</span>';
                        break;
                }
                return $status;
            })
            ->editColumn('lines_sum_debit', function ($row) {
                $total = $row->lines_sum_debit ?? 0;

                $total_text = convertCurrency($total, true, 2);

                if ($row->type == 'cash_in') {
                    $total_text = '<span class="text-success">+' . $total_text . '</span>';
                }
                if ($row->type == 'cash_out') {
                    $total_text = '<span class="text-danger">-' . $total_text . '</span>';
                }

                return $total_text;
            })
            ->filterColumn('lines_sum_debit', function ($query, $keyword) {
                
            })
            ->addColumn('type', function ($row) {
                if ($row->type == 'cash_in') {
                    return '<span class="badge bg-success">Masuk</span>';
                } else if ($row->type == 'cash_out') {
                    return '<span class="badge bg-danger">Keluar</span>';
                } else if ($row->type == 'cash_transfer') {
                    return '<span class="badge bg-primary">Transfer Kas</span>';
                }
                return ucfirst(str_replace('_', ' ', $row->type));
            })
            ->rawColumns([
                'code',
                'date',
                'description',
                'lines_sum_debit',
                'type',
                'status',
            ])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $type = request()->get('type', 'in');

        $categories = Account::where('is_cash', '=', false)
            ->whereIn('cash_flow_type', $type == 'in' ? ['in'] : ['out'])
            ->whereIn('sort', $type == 'in' ? [4] : [6])
            ->get();

        $accounts = Account::where('is_cash', '=', true)
            ->get();


        return $this->viewTenant(
            'cash.cash-flows.create',
            'Tambah Arus Kas ' . ($type == 'in' ? 'Masuk' : 'Keluar'),
            $this->groupMenu(),
            compact(
                'type',
                'categories',
                'accounts'
            ),
            [
                ['Kas' => ''],
                ['Arus Kas' => route('cash-flows.index')],
                ['Tambah Arus Kas ' . ($type == 'in' ? 'Masuk' : 'Keluar') => 'javascript:void(0)'],
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $tenant = request()->attributes->get('tenant');

        $validated = $request->validate(
            [
                'date' => 'required|date',
                'type' => 'required|in:in,out',
                'description' => 'nullable|string|max:1000',
                'reference' => 'nullable|string|max:255',
                'account_id' => 'required|exists:accounts,id',
                'lines' => 'required|array|min:1',
                'lines.*.category_id' => 'required|exists:accounts,id',
                'lines.*.description' => 'nullable|string|max:1000',
                'lines.*.amount' => 'required|numeric|min:0.01',
            ],
            [
                'lines.required' => 'Harap tambahkan minimal satu baris arus kas.',
                'lines.min' => 'Harap tambahkan minimal satu baris arus kas.',
                'lines.*.category_id.exists' => 'Kategori pada baris arus kas tidak valid.',
                'lines.*.amount.min' => 'Jumlah pada baris arus kas harus lebih dari 0.',
                'lines.*.amount.required' => 'Jumlah pada baris arus kas wajib diisi.',
                'lines.*.description.max' => 'Keterangan pada baris arus kas maksimal 1000 karakter.',
                'lines.*.category_id.required' => 'Kategori pada baris arus kas wajib diisi.',
                'date.required' => 'Tanggal wajib diisi.',
                'date.date' => 'Format tanggal tidak valid.',
                'type.required' => 'Tipe arus kas wajib diisi.',
                'type.in' => 'Tipe arus kas tidak valid.',
                'account_id.required' => 'Akun kas wajib diisi.',
                'account_id.exists' => 'Akun kas tidak valid.',
            ]
        );

        $account_id = $validated['account_id'];
        $date = $validated['date'];
        $description = $validated['description'] ?? '';
        $reference = $validated['reference'] ?? '';
        $cash_flow_type = $validated['type'];
        $lines = $validated['lines'];


        $account = Account::find($account_id);
        if (!$account || !$account->is_cash) {
            if ($cash_flow_type == 'in') {
                return back()->withInput()->withErrors(['account_id' => 'Akun Masuk Ke tidak valid.']);
            } else if ($cash_flow_type == 'out') {
                return back()->withInput()->withErrors(['account_id' => 'Akun Keluar Dari tidak valid.']);
            }
        }

        $line_account_ids = collect($lines)->pluck('category_id')->toArray();
        $existing_accounts = Account::whereIn('id', $line_account_ids);
        $existing_accounts_ids = $existing_accounts->pluck('id')->toArray();
        $existing_accounts_list = $existing_accounts->get()->groupBy('id');


        $flag = false;
        foreach ($line_account_ids as $account_id) {
            if (!in_array($account_id, $existing_accounts_ids)) {
                $flag = true;
                break;
            }

            $existing_account = $existing_accounts_list[$account_id]->first();
            if ($existing_account->cash_flow_type != $cash_flow_type) {
                return back()->withInput()->withErrors(['lines' => 'Tipe kategori pada detail arus kas [' . $existing_account->name . '] tidak sesuai dengan tipe arus kas.']);
            }
        }

        DB::beginTransaction();
        try {
            $code = next_sequence('cash_flow_' . $cash_flow_type);


            // create journal
            $journal = Journal::create([
                'code' => $code,
                'type' => 'cash_' . $cash_flow_type,
                'date' => $date,
                'description' => $description,
                'reference' => $reference,
                'created_by' => Auth::id(),
            ]);

            $total_amount = 0;
            foreach ($lines as $line) {
                $amount = $line['amount'];
                if ($cash_flow_type == 'in') {
                    // cash flow type in, credit journal line
                    $journal->lines()->create([
                        'account_id' => $line['category_id'],
                        'debit' => 0,
                        'credit' => $amount,
                        'description' => $line['description'] ?? null,
                    ]);
                } else {
                    // cash flow type out, debit journal line
                    $journal->lines()->create([
                        'account_id' => $line['category_id'],
                        'debit' => $amount,
                        'credit' => 0,
                        'description' => $line['description'] ?? null,
                    ]);
                }

                $total_amount += $amount;
            }

            // create journal line for source/target account
            if ($cash_flow_type == 'in') {
                // cash flow type in, debit source/target account
                $journal->lines()->create([
                    'account_id' => $account->id,
                    'debit' => $total_amount,
                    'credit' => 0,
                    'description' => 'Arus kas masuk ke ' . $account->name,
                ]);
            } else {
                // cash flow type out, credit source/target account
                $journal->lines()->create([
                    'account_id' => $account->id,
                    'debit' => 0,
                    'credit' => $total_amount,
                    'description' => 'Arus kas keluar dari ' . $account->name,
                ]);
            }

            // auto post journal (store posted_at in UTC)
            $journal->status = 'posted';
            // use UTC time to ensure DB stores timestamp in UTC
            $journal->posted_at = now()->utc();
            $journal->save();

            DB::commit();
            return redirect()->route('cash-flows.show', [
                'tenant_id' => $tenant->tenant_id,
                'cash_flow' => $journal->id
            ])->with('success', 'Arus kas berhasil ditambahkan dan diposting.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan arus kas: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $tenant_id, string $id)
    {
        $cashFlow = Journal::with(['lines.account'])->findOrFail($id);

        $type = $cashFlow->type == 'cash_in' ? 'in' : 'out';

        $account = $cashFlow->lines()->whereHas('account', function ($query) {
            $query->where('is_cash', '=', true);
        })->first()->account;

        $lines = $cashFlow->lines()->where('account_id', '!=', $account->id)->get();

        return $this->viewTenant(
            'cash.cash-flows.show',
            'Detail Arus Kas ' . ($cashFlow->type == 'cash_in' ? 'Masuk' : 'Keluar'),
            $this->groupMenu(),
            compact('cashFlow', 'type', 'account', 'lines'),
            [
                ['Kas' => ''],
                ['Arus Kas' => route('cash-flows.index')],
                ['Detail Arus Kas ' . ($cashFlow->type == 'cash_in' ? 'Masuk' : 'Keluar') => 'javascript:void(0)'],
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($tenant_id, string $id)
    {
        $type = request()->get('type', 'in');

        $cashFlow = Journal::with(['lines.account'])->findOrFail($id);
        $type = $cashFlow->type == 'cash_in' ? 'in' : 'out';


        $selectedAccount = $cashFlow->lines()->whereHas('account', function ($query) {
            $query->where('is_cash', '=', true);
        })->first()->account;
        $lines = $cashFlow->lines()->where('account_id', '!=', $selectedAccount->id)->get();

        $categories = Account::where('is_cash', '=', false)
            ->whereIn('cash_flow_type', $type == 'in' ? ['in'] : ['out'])
            ->whereIn('sort', $type == 'in' ? [4] : [6])
            ->get();

        $accounts = Account::where('is_cash', '=', true)
            ->get();



        return $this->viewTenant(
            'cash.cash-flows.edit',
            'Ubah Arus Kas ' . ($type == 'in' ? 'Masuk' : 'Keluar'),
            $this->groupMenu(),
            compact(
                'type',
                'categories',
                'accounts',
                'cashFlow',
                'type',
                'selectedAccount',
                'lines'
            ),
            [
                ['Kas' => ''],
                ['Arus Kas' => route('cash-flows.index')],
                ['Tambah Arus Kas ' . ($type == 'in' ? 'Masuk' : 'Keluar') => 'javascript:void(0)'],
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $tenant_id, string $id)
    {
        $tenant = request()->attributes->get('tenant');

        // dd($request->all());
        $validated = $request->validate(
            [
                'id' => 'required|exists:journals,id',
                'date' => 'required|date',
                'type' => 'required|in:in,out',
                'description' => 'nullable|string|max:1000',
                'reference' => 'nullable|string|max:255',
                'account_id' => 'required|exists:accounts,id',
                'lines' => 'required|array|min:1',
                'lines.*.category_id' => 'required|exists:accounts,id',
                'lines.*.description' => 'nullable|string|max:1000',
                'lines.*.amount' => 'required|numeric|min:0.01',
            ],
            [
                'id.exists' => 'Arus Kas yang diubah tidak valid.',
                'lines.required' => 'Harap tambahkan minimal satu baris arus kas.',
                'lines.min' => 'Harap tambahkan minimal satu baris arus kas.',
                'lines.*.category_id.exists' => 'Kategori pada baris arus kas tidak valid.',
                'lines.*.amount.min' => 'Jumlah pada baris arus kas harus lebih dari 0.',
                'lines.*.amount.required' => 'Jumlah pada baris arus kas wajib diisi.',
                'lines.*.description.max' => 'Keterangan pada baris arus kas maksimal 1000 karakter.',
                'lines.*.category_id.required' => 'Kategori pada baris arus kas wajib diisi.',
                'date.required' => 'Tanggal wajib diisi.',
                'date.date' => 'Format tanggal tidak valid.',
                'type.required' => 'Tipe arus kas wajib diisi.',
                'type.in' => 'Tipe arus kas tidak valid.',
                'account_id.required' => 'Akun kas wajib diisi.',
                'account_id.exists' => 'Akun kas tidak valid.',
            ]
        );


        $account_id = $validated['account_id'];
        $date = $validated['date'];
        $description = $validated['description'] ?? '';
        $reference = $validated['reference'] ?? '';
        $cash_flow_type = $validated['type'];
        $lines = $validated['lines'];

        $journal = Journal::findOrFail($id);
        // dd($journal->toArray());
        if ($journal->status == 'posted') {
            return back()->withInput()->withErrors(['header' => 'Arus Kas yang sudah diposting tidak dapat diubah.']);
        }


        $account = Account::find($account_id);
        if (!$account || !$account->is_cash) {
            if ($cash_flow_type == 'in') {
                return back()->withInput()->withErrors(['account_id' => 'Akun Masuk Ke tidak valid.']);
            } else if ($cash_flow_type == 'out') {
                return back()->withInput()->withErrors(['account_id' => 'Akun Keluar Dari tidak valid.']);
            }
        }

        $line_account_ids = collect($lines)->pluck('category_id')->toArray();
        $existing_accounts = Account::whereIn('id', $line_account_ids);
        $existing_accounts_ids = $existing_accounts->pluck('id')->toArray();
        $existing_accounts_list = $existing_accounts->get()->groupBy('id');


        $flag = false;
        foreach ($line_account_ids as $account_id) {
            if (!in_array($account_id, $existing_accounts_ids)) {
                $flag = true;
                break;
            }

            $existing_account = $existing_accounts_list[$account_id]->first();
            if ($existing_account->cash_flow_type != $cash_flow_type) {
                return back()->withInput()->withErrors(['lines' => 'Tipe kategori pada detail arus kas [' . $existing_account->name . '] tidak sesuai dengan tipe arus kas.']);
            }
        }

        DB::beginTransaction();
        try {


            // update journal header
            $journal->update([
                'type' => 'cash_' . $cash_flow_type,
                'date' => $date,
                'description' => $description,
                'reference' => $reference,
            ]);

            // hapus journal lines
            $journal->lines()->delete();

            $total_amount = 0;
            foreach ($lines as $line) {
                $amount = $line['amount'];
                if ($cash_flow_type == 'in') {
                    // cash flow type in, credit journal line
                    $journal->lines()->create([
                        'account_id' => $line['category_id'],
                        'debit' => 0,
                        'credit' => $amount,
                        'description' => $line['description'] ?? null,
                    ]);
                } else {
                    // cash flow type out, debit journal line
                    $journal->lines()->create([
                        'account_id' => $line['category_id'],
                        'debit' => $amount,
                        'credit' => 0,
                        'description' => $line['description'] ?? null,
                    ]);
                }

                $total_amount += $amount;
            }

            // create journal line for source/target account
            if ($cash_flow_type == 'in') {
                // cash flow type in, debit source/target account
                $journal->lines()->create([
                    'account_id' => $account->id,
                    'debit' => $total_amount,
                    'credit' => 0,
                    'description' => 'Arus kas masuk ke ' . $account->name,
                ]);
            } else {
                // cash flow type out, credit source/target account
                $journal->lines()->create([
                    'account_id' => $account->id,
                    'debit' => 0,
                    'credit' => $total_amount,
                    'description' => 'Arus kas keluar dari ' . $account->name,
                ]);
            }

            DB::commit();
            return redirect()->route('cash-flows.show', [
                'tenant_id' => $tenant->tenant_id,
                'cash_flow' => $journal->id
            ])->with('success', 'Berhasil menyimpan perubahan arus kas.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan perubahan arus kas: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($tenant_id, string $id)
    {
        $cashFlow = Journal::findOrFail($id);
        if ($cashFlow->status == 'posted') {
            return response()->json([
                'status' => 'error',
                'message' => 'Arus Kas yang sudah diposting tidak dapat dihapus.'
            ], 400);
        }
        $cashFlow->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil menghapus Arus Kas.'
        ], 200);
    }

    public function setDraft(Request $request, $tenant_id, string $id)
    {
        $tenant = request()->attributes->get('tenant');

        $journal = Journal::findOrFail($id);
        if ($journal->status != 'posted') {
            return back()->withInput()->withErrors(['header' => 'Hanya Arus Kas yang diposting yang dapat diubah ke draft.']);
        }

        DB::beginTransaction();
        try {
            // set journal to draft
            $journal->status = 'draft';
            $journal->posted_at = null;
            $journal->save();

            DB::commit();
            return redirect()->route('cash-flows.edit', [
                'tenant_id' => $tenant->tenant_id,
                'cash_flow' => $journal->id
            ])->with('success', 'Berhasil mengubah Arus Kas menjadi draft.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info('Error setDraft CashFlow: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengubah Arus Kas menjadi draft: ' . $e->getMessage());
        }
    }

    public function setPosted(Request $request, $tenant_id, string $id)
    {
        $tenant = request()->attributes->get('tenant');

        $journal = Journal::findOrFail($id);
        if ($journal->status != 'draft') {
            return back()->withInput()->withErrors(['header' => 'Hanya Arus Kas yang draft yang dapat diubah ke diposting.']);
        }

        DB::beginTransaction();
        try {
            // set journal to draft
            $journal->status = 'posted';
            $journal->posted_at = now()->utc();
            $journal->save();

            DB::commit();
            return redirect()->route('cash-flows.edit', [
                'tenant_id' => $tenant->tenant_id,
                'cash_flow' => $journal->id
            ])->with('success', 'Berhasil mengubah Arus Kas menjadi Diposting.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info('Error setPosted CashFlow: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengubah Arus Kas menjadi Diposting: ' . $e->getMessage());
        }
    }
}
