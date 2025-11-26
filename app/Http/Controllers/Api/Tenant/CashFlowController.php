<?php

namespace App\Http\Controllers\Api\Tenant;

use Illuminate\Http\Request;
use App\Models\Tenant\Journal;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use App\Models\Tenant\Account;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;

class CashFlowController extends ApiController
{
    public function index()
    {
        $type = request()->route('type', 'in');

        // Eager-load related account on lines and compute sum of lines.debit per journal
        $cashFlowIns = Journal::select([
            'id',
            'code',
            'date',
            'description',
            'reference',
            'status',
            'posted_at',
        ])
            ->when($type, function ($query) use ($type) {
                if ($type == 'in') return $query->cashFlowIn();
                return $query->cashFlowOut();
            })
            ->with(['lines.account'])
            ->withSum('lines', 'debit')
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($journal) {
                // hide lines

                // withSum creates property lines_sum_debit
                $total = $journal->lines_sum_debit ?? 0;

                $journal['total'] = (float) $total;

                unset($journal->lines);
                unset($journal->lines_sum_debit);

                $journal->reference = $journal->reference ?? "";
                $journal->description = $journal->description ?? "";

                // convert posted_at (stored in UTC) to timezone +08:00 for response
                $journal->posted_at = $journal->posted_at ? Carbon::parse($journal->posted_at)->setTimezone('+08:00')->toIso8601String() : null;

                return $journal;
            });

        return $this->responseSuccess([
            "cash_flow_ins" => $cashFlowIns
        ]);
    }
    public function show(Request $request, $tenant_id, $id)
    {
        $journal = Journal::where('id', '=', $id)
            ->with(['createdBy', 'updatedBy'])
            ->withSum('lines', 'debit')
            ->withSum('lines', 'credit')
            ->get();
        if ($journal->isEmpty()) {
            return $this->responseError('Arus kas tidak ditemukan', 404);
        }
        $journals = $journal->map(function ($journal) {
            // convert posted_at (stored in UTC) to timezone +07:00 for response
            $journal->posted_at = parseTimezone($journal->posted_at);
            $journal->created_at = parseTimezone($journal->created_at);
            $journal->updated_at = parseTimezone($journal->updated_at);

            return $journal;
        });
        $journal = $journals->first();

        // get auth user merchant
        $tenant = $request->attributes->get('tenant');

        $journalLines = $journal->lines()
            ->with('account')
            ->get();

        $mappedLines = $journalLines->map(function ($line) {
            $line->account; // load account relation

            // Jangan tampilkan metadata pada account di setiap line
            if ($line->relationLoaded('account') && $line->account) {
                $line->account->makeHidden([
                    'sort',
                    'is_cash',
                    'cash_flow_type',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'deleted_at',
                    'deleted_by',
                ]);
            }

            $line->debit = (float) $line->debit;
            $line->credit = (float) $line->credit;

            $line->makeHidden([
                'created_at',
                'updated_at',
                'created_by',
                'updated_by',
                'deleted_at',
                'deleted_by'
            ]);

            return $line;
        });

        // Use setRelation to attach the mapped collection as the 'lines' relation on the model
        $journal->setRelation('lines', $mappedLines);


        return $this->responseSuccess(
            ["cash_flow" => $journal]
        );
    }

    public function store(Request $request)
    {
        $tenant = request()->attributes->get('tenant');
<<<<<<< HEAD
=======

>>>>>>> origin/main
        $validatedData = $request->validate([
            'cash_flow_type' => 'required|string|max:255|in:in,out',
            'date' => 'required|date|date_format:Y-m-d',
            'description' => 'nullable|string',
            'reference' => 'nullable|string',

            'account_id' => 'required|integer|exists:accounts,id',
            'lines' => 'required|array|min:1',
            'lines.*.account_id' => 'required|integer|exists:accounts,id',
            'lines.*.amount' => 'required|numeric|min:0',
            'lines.*.description' => 'required|string',
        ], [
            'cash_flow_type.in' => 'Tipe arus kas yang dipilih tidak valid. Tipe yang diizinkan adalah in, out.',
            'account_id.exists' => 'Akun sumber/tujuan yang dipilih tidak valid.',
            'lines.required' => 'Setidaknya harus ada satu baris detail arus kas.',
            'lines.*.account_id.exists' => 'Akun yang dipilih tidak valid.',
            'lines.*.amount.min' => 'Jumlah harus lebih besar dari nol.',
            'lines.*.description.required' => 'Deskripsi pada detail arus kas harus diisi.',
        ]);

        $account = Account::where('id', $validatedData['account_id'])->first();
        if (!$account) {
            return $this->responseError('Akun sumber/tujuan tidak ditemukan', 404);
        }


        $cash_flow_type = $validatedData['cash_flow_type'];
        $date = $validatedData['date'] ?? date("Y-m-d");
        // $date should not greater than today
        if ($date > date("Y-m-d")) {
            return $this->responseError('Tanggal arus kas tidak boleh lebih besar dari hari ini.', 422);
        }

        $lines = $validatedData['lines'] ?? [];
        if (empty($lines)) {
            return $this->responseError('Setidaknya harus ada satu baris detail arus kas.', 422);
        }
        // $lines = array_map(function ($line) {
        //     return [
        //         'account_id' => $line['account_id'],
        //         'amount' => $line['amount'],
        //         'description' => $line['description'] ?? null,
        //     ];
        // }, $lines);

        // convert lines to collection
        $lines = collect($lines);

        // check if lines.*.amount <= 0
        foreach ($lines as $line) {
            if ($line['amount'] <= 0) {
                return $this->responseError('Jumlah pada detail arus kas harus lebih besar dari nol.', 422);
            }
        }

        $line_account_ids = $lines->pluck('account_id')->toArray();
        // check if all account_id in lines exist
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
                return $this->responseError('Tipe akun pada detail arus kas [' . $existing_account->code . '] tidak sesuai dengan tipe arus kas.', 422);
            }
        }

        DB::beginTransaction();
        try {
            $code = next_sequence('cash_flow_' . $cash_flow_type);

            // create journal
            $journal = Journal::create([
                'code' => $code,
                'type' => 'cash_' . $cash_flow_type,
                'date' => $validatedData['date'],
                'description' => $validatedData['description'] ?? null,
                'reference' => $validatedData['reference'] ?? null,
                'created_by' => Auth::id(),
            ]);

            $total_amount = 0;
            foreach ($lines as $line) {
                $amount = $line['amount'];
                if ($cash_flow_type == 'in') {
                    // cash flow type in, credit journal line
                    $journal->lines()->create([
                        'account_id' => $line['account_id'],
                        'debit' => 0,
                        'credit' => $amount,
                        'description' => $line['description'] ?? null,
                    ]);
                } else {
                    // cash flow type out, debit journal line
                    $journal->lines()->create([
                        'account_id' => $line['account_id'],
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
            return $this->responseSuccess(
                ['cash_flow' => $journal],
                "Berhasil menambahkan arus kas baru."
            );
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan saat menambahkan arus kas baru.', 500);
        }
    }

    public function update(Request $request, $tenant_id, $id)
    {
        $tenant = request()->attributes->get('tenant');

        $validatedData = $request->validate([
            'cash_flow_type' => 'required|string|max:255|in:in,out',
            'date' => 'required|date|date_format:Y-m-d',
            'description' => 'nullable|string',
            'reference' => 'nullable|string',

            'account_id' => 'required|integer|exists:accounts,id',
            'lines' => 'required|array|min:1',
            'lines.*.account_id' => 'required|integer|exists:accounts,id',
            'lines.*.amount' => 'required|numeric|min:0',
            'lines.*.description' => 'required|string',
        ], [
            'cash_flow_type.in' => 'Tipe arus kas yang dipilih tidak valid. Tipe yang diizinkan adalah in, out.',
            'account_id.exists' => 'Akun sumber/tujuan yang dipilih tidak valid.',
            'lines.required' => 'Setidaknya harus ada satu baris detail arus kas.',
            'lines.*.account_id.exists' => 'Akun yang dipilih tidak valid.',
            'lines.*.amount.min' => 'Jumlah harus lebih besar dari nol.',
            'lines.*.description.required' => 'Deskripsi pada detail arus kas harus diisi.',
        ]);

        $journal = Journal::where('id', '=', $id)->first();
        if (!$journal) {
            return $this->responseError('Arus kas yang akan diubah tidak ditemukan', 404);
        }
        if ($journal->status == 'posted') {
            return $this->responseError('Arus kas yang sudah diposting tidak boleh diubah', 422);
        }

        $account = Account::where('id', $validatedData['account_id'])->first();
        if (!$account) {
            return $this->responseError('Akun sumber/tujuan tidak ditemukan', 404);
        }

        $cash_flow_type = $validatedData['cash_flow_type'];
        $date = $validatedData['date'] ?? date("Y-m-d");
        // $date should not greater than today
        if ($date > date("Y-m-d")) {
            return $this->responseError('Tanggal arus kas tidak boleh lebih besar dari hari ini.', 422);
        }

        $lines = $validatedData['lines'] ?? [];
        if (empty($lines)) {
            return $this->responseError('Setidaknya harus ada satu baris detail arus kas.', 422);
        }

        // convert lines to collection
        $lines = collect($lines);

        // check if lines.*.amount <= 0
        foreach ($lines as $line) {
            if ($line['amount'] <= 0) {
                return $this->responseError('Jumlah pada detail arus kas harus lebih besar dari nol.', 422);
            }
        }

        $line_account_ids = $lines->pluck('account_id')->toArray();

        // check if all account_id in lines exist
        $existing_accounts = Account::whereIn('id', $line_account_ids);
        $existing_accounts_ids = $existing_accounts->pluck('id')->toArray();
        $existing_accounts_list = $existing_accounts->get()->groupBy('id');

        foreach ($line_account_ids as $account_id) {
            $existing_account = $existing_accounts_list[$account_id]->first();
            if ($existing_account->cash_flow_type != $cash_flow_type) {
                return $this->responseError('Tipe akun pada detail arus kas [' . $existing_account->code . '] tidak sesuai dengan tipe arus kas.', 422);
            }
        }

        DB::beginTransaction();
        try {


            // update journal header
            $journal->update([
                'type' => 'cash_' . $cash_flow_type,
                'date' => $validatedData['date'],
                'description' => $validatedData['description'] ?? null,
                'reference' => $validatedData['reference'] ?? null,
            ]);

            // hapus journal lines
            $journal->lines()->delete();

            $total_amount = 0;
            foreach ($lines as $line) {
                $amount = $line['amount'];
                if ($cash_flow_type == 'in') {
                    // cash flow type in, credit journal line
                    $journal->lines()->create([
                        'account_id' => $line['account_id'],
                        'debit' => 0,
                        'credit' => $amount,
                        'description' => $line['description'] ?? null,
                    ]);
                } else {
                    // cash flow type out, debit journal line
                    $journal->lines()->create([
                        'account_id' => $line['account_id'],
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
            return $this->responseSuccess(
                ['cash_flow' => $journal],
                "Berhasil menyimpan perubahan arus kas."
            );
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan saat menyimpan perubahan arus kas.', 500);
        }
    }

    public function set_draft(Request $request, $tenant_id, $id)
    {
        $tenant = request()->attributes->get('tenant');

        $journal = Journal::where('id', '=', $id)->first();
        if (!$journal) {
            return $this->responseError('Arus kas yang akan diubah tidak ditemukan', 404);
        }
        if ($journal->status == 'draft') {
            return $this->responseError('Arus kas sudah dalam status draft.', 422);
        }

        DB::beginTransaction();
        try {
            // set journal to draft
            $journal->status = 'draft';
            $journal->posted_at = null;
            $journal->save();

            DB::commit();
            return $this->responseSuccess(
                [],
                "Berhasil mengubah arus kas ke status draft."
            );
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan saat mengubah status arus kas ke draft.', 500);
        }
    }

    public function set_posted(Request $request, $tenant_id, $id)
    {
        $tenant = request()->attributes->get('tenant');

        $journal = Journal::where('id', '=', $id)->first();
        if (!$journal) {
            return $this->responseError('Arus kas yang akan diubah tidak ditemukan', 404);
        }
        if ($journal->status == 'posted') {
            return $this->responseError('Arus kas sudah dalam status posted.', 422);
        }

        // periksa total debit dan credit harus sama
        $total_debit = $journal->lines()->sum('debit');
        $total_credit = $journal->lines()->sum('credit');
        if ($total_debit != $total_credit) {
            return $this->responseError('Total debit dan kredit pada arus kas tidak seimbang.', 422);
        }

        DB::beginTransaction();
        try {
            // set journal to posted
            $journal->status = 'posted';
            // use UTC time to ensure DB stores timestamp in UTC
            $journal->posted_at = now()->utc();
            $journal->save();

            DB::commit();
            return $this->responseSuccess(
                [],
                "Berhasil memposting arus kas."
            );
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan saat memposting arus kas.', 500);
        }
    }

    public function destroy(Request $request, $tenant_id, $id)
    {
        $tenant = request()->attributes->get('tenant');

        $journal = Journal::where('id', '=', $id)->first();
        if (!$journal) {
            return $this->responseError('Arus kas yang akan dihapus tidak ditemukan', 404);
        }
        if ($journal->status == 'posted') {
            return $this->responseError('Arus kas yang sudah diposting tidak bisa dihapus', 422);
        }

        DB::beginTransaction();
        try {
            // delete journal
            $journal->delete();

            DB::commit();
            return $this->responseSuccess(
                [],
                "Berhasil menghapus arus kas."
            );
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan saat menghapus arus kas.', 500);
        }
    }
}
