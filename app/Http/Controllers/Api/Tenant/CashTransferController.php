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

class CashTransferController extends ApiController
{
    public function index()
    {

        // Eager-load related account on lines and compute sum of lines.debit per journal
        $transfers = Journal::select([
            'id',
            'code',
            'date',
            'description',
            'reference',
            'status',
            'posted_at',
        ])
            ->cashTransfer()
            ->with(['lines.account'])
            ->withSum('lines', 'debit')
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($journal) {
                // hide lines

                $amount = $journal->lines_sum_debit ?? 0;
                $journal->amount = (float) $amount;

                // check if lines is debit
                foreach ($journal->lines as $line) {
                    if ($line->credit > 0) {
                        $journal->fromAccount = [
                            'id' => $line->account->id,
                            'name' => $line->account->name,
                            'code' => $line->account->code,
                        ];
                    }

                    if ($line->debit > 0) {
                        $journal->toAccount = [
                            'id' => $line->account->id,
                            'name' => $line->account->name,
                            'code' => $line->account->code,
                        ];
                    }
                }


                unset($journal->lines);
                unset($journal->lines_sum_debit);

                $journal->reference = $journal->reference ?? "";
                $journal->description = $journal->description ?? "";

                // convert posted_at (stored in UTC) to timezone +08:00 for response
                $journal->posted_at = $journal->posted_at ? Carbon::parse($journal->posted_at)->setTimezone('+08:00')->toIso8601String() : null;

                return $journal;
            });

        return $this->responseSuccess([
            "transfers" => $transfers
        ]);
    }
    public function show(Request $request, $tenant_id, $id)
    {
        $journal = Journal::cashTransfer()->where('id', '=', $id)
            ->with(['createdBy', 'updatedBy'])
            ->withSum('lines', 'debit')
            ->get();
        if ($journal->isEmpty()) {
            return $this->responseError('Pindah buku tidak ditemukan', 404);
        }
        $journals = $journal->map(function ($journal) {
            // convert posted_at (stored in UTC) to timezone +07:00 for response
            $journal->posted_at = parseTimezone($journal->posted_at);
            $journal->created_at = parseTimezone($journal->created_at);
            $journal->updated_at = parseTimezone($journal->updated_at);

            $journal->amount = (float) ($journal->lines_sum_debit ?? 0);

            // check if lines is debit
            foreach ($journal->lines as $line) {
                if ($line->credit > 0) {
                    $journal->fromAccount = [
                        'id' => $line->account->id,
                        'name' => $line->account->name,
                        'code' => $line->account->code,
                    ];
                }

                if ($line->debit > 0) {
                    $journal->toAccount = [
                        'id' => $line->account->id,
                        'name' => $line->account->name,
                        'code' => $line->account->code,
                    ];
                }
            }


            unset($journal->lines);
            unset($journal->lines_sum_debit);

            $journal->reference = $journal->reference ?? "";
            $journal->description = $journal->description ?? "";

            // convert posted_at (stored in UTC) to timezone +08:00 for response
            $journal->posted_at = $journal->posted_at ? Carbon::parse($journal->posted_at)->setTimezone('+08:00')->toIso8601String() : null;


            return $journal;
        });
        $journal = $journals->first();

        // get auth user merchant
        $tenant = $request->attributes->get('tenant');

        $journalLines = $journal->lines()
            ->with('account')
            ->get();

        // $mappedLines = $journalLines->map(function ($line) {
        //     $line->account; // load account relation

        //     // Jangan tampilkan metadata pada account di setiap line
        //     if ($line->relationLoaded('account') && $line->account) {
        //         $line->account->makeHidden([
        //             'sort',
        //             'is_cash',
        //             'cash_flow_type',
        //             'created_at',
        //             'updated_at',
        //             'created_by',
        //             'updated_by',
        //             'deleted_at',
        //             'deleted_by',
        //         ]);
        //     }

        //     $line->debit = (float) $line->debit;
        //     $line->credit = (float) $line->credit;

        //     $line->makeHidden([
        //         'created_at',
        //         'updated_at',
        //         'created_by',
        //         'updated_by',
        //         'deleted_at',
        //         'deleted_by'
        //     ]);

        //     return $line;
        // });

        // // Use setRelation to attach the mapped collection as the 'lines' relation on the model
        // $journal->setRelation('lines', $mappedLines);


        return $this->responseSuccess(
            ["cash_flow" => $journal]
        );
    }

    public function store(Request $request)
    {
        $tenant = request()->attributes->get('tenant');

        $validatedData = $request->validate([
            'date' => 'required|date|date_format:Y-m-d',
            'description' => 'nullable|string',
            'reference' => 'nullable|string',

            'from_account_id' => 'required|integer|exists:accounts,id',
            'to_account_id' => 'required|integer|exists:accounts,id',
            'amount' => 'required',
        ], [
            'from_account_id.exists' => 'Akun sumber yang dipilih tidak valid.',
            'to_account_id.exists' => 'Akun tujuan yang dipilih tidak valid.',
            'amount.required' => 'Jumlah harus diisi',
        ]);

        $reference = $validatedData['reference'] ?? '';
        $description = $validatedData['description'] ?? '';

        $amount = convertCurrency($validatedData['amount']);
        if ($amount <= 0) {
            return $this->responseError('Jumlah harus diisi');
        }

        $from_account = Account::where('id', $validatedData['from_account_id'])
            ->where('is_cash', '=', 1)
            ->first();
        if (!$from_account) {
            return $this->responseError('Akun sumber tidak ditemukan', 404);
        }

        $to_account = Account::where('id', $validatedData['to_account_id'])
            ->where('is_cash', '=', 1)
            ->first();
        if (!$to_account) {
            return $this->responseError('Akun tujuan tidak ditemukan', 404);
        }

        $date = $validatedData['date'] ?? date("Y-m-d");
        // $date should not greater than today
        if ($date > date("Y-m-d")) {
            return $this->responseError('Tanggal pindah buku tidak boleh lebih besar dari hari ini.', 422);
        }

        DB::beginTransaction();
        try {
            $code = next_sequence('cash_transfer');

            // create journal
            $journal = Journal::create([
                'code' => $code,
                'type' => 'cash_transfer',
                'date' => $validatedData['date'],
                'description' => $description,
                'reference' => $reference,
                'created_by' => Auth::id(),
            ]);

            // credit from source account id
            $journal->lines()->create([
                'account_id' => $from_account->id,
                'debit' => 0,
                'credit' => $amount,
                'description' => $description,
            ]);

            // debit to target account id
            $journal->lines()->create([
                'account_id' => $to_account->id,
                'debit' => $amount,
                'credit' => 0,
                'description' => $description,
            ]);


            // auto post journal (store posted_at in UTC)
            $journal->status = 'posted';
            // use UTC time to ensure DB stores timestamp in UTC
            $journal->posted_at = now()->utc();
            $journal->save();

            DB::commit();
            return $this->responseSuccess(
                ['transfer' => $journal],
                "Berhasil menambahkan pindah buku baru."
            );
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan saat menambahkan pindah buku baru.', 500);
        }
    }

    public function update(Request $request, $tenant_id, $id)
    {
        $tenant = request()->attributes->get('tenant');

        $validatedData = $request->validate([
            'date' => 'required|date|date_format:Y-m-d',
            'description' => 'nullable|string',
            'reference' => 'nullable|string',

            'from_account_id' => 'required|integer|exists:accounts,id',
            'to_account_id' => 'required|integer|exists:accounts,id',
            'amount' => 'required',
        ], [
            'from_account_id.exists' => 'Akun sumber yang dipilih tidak valid.',
            'to_account_id.exists' => 'Akun tujuan yang dipilih tidak valid.',
            'amount.required' => 'Jumlah harus diisi',
        ]);

        $journal = Journal::cashTransfer()->where('id', '=', $id)->first();
        if (!$journal) {
            return $this->responseError('Pindah buku yang akan diubah tidak ditemukan', 404);
        }
        if ($journal->status == 'posted') {
            return $this->responseError('Pindah buku yang sudah diposting tidak boleh diubah', 422);
        }

        $reference = $validatedData['reference'] ?? '';
        $description = $validatedData['description'] ?? '';

        $amount = convertCurrency($validatedData['amount']);
        if ($amount <= 0) {
            return $this->responseError('Jumlah harus diisi');
        }

        $from_account = Account::where('id', $validatedData['from_account_id'])
            ->where('is_cash', '=', 1)
            ->first();
        if (!$from_account) {
            return $this->responseError('Akun sumber tidak ditemukan', 404);
        }

        $to_account = Account::where('id', $validatedData['to_account_id'])
            ->where('is_cash', '=', 1)
            ->first();
        if (!$to_account) {
            return $this->responseError('Akun tujuan tidak ditemukan', 404);
        }

        $date = $validatedData['date'] ?? date("Y-m-d");
        // $date should not greater than today
        if ($date > date("Y-m-d")) {
            return $this->responseError('Tanggal pindah buku tidak boleh lebih besar dari hari ini.', 422);
        }

        DB::beginTransaction();
        try {


            // update journal header
            $journal->update([
                'type' => 'cash_transfer',
                'date' => $validatedData['date'],
                'description' => $validatedData['description'] ?? null,
                'reference' => $validatedData['reference'] ?? null,
            ]);

            // hapus journal lines
            $journal->lines()->delete();

            // credit from source account id
            $journal->lines()->create([
                'account_id' => $from_account->id,
                'debit' => 0,
                'credit' => $amount,
                'description' => $description,
            ]);

            // debit to target account id
            $journal->lines()->create([
                'account_id' => $to_account->id,
                'debit' => $amount,
                'credit' => 0,
                'description' => $description,
            ]);

            DB::commit();
            return $this->responseSuccess(
                ['transfer' => $journal],
                "Berhasil menyimpan perubahan pindah buku."
            );
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan saat menyimpan perubahan pindah buku.', 500);
        }
    }

    public function set_draft(Request $request, $tenant_id, $id)
    {
        $tenant = request()->attributes->get('tenant');

        $journal = Journal::cashTransfer()->where('id', '=', $id)->first();
        if (!$journal) {
            return $this->responseError('Pindah buku yang akan diubah tidak ditemukan', 404);
        }
        if ($journal->status == 'draft') {
            return $this->responseError('Pindah buku sudah dalam status draft.', 422);
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
                "Berhasil mengubah pindah buku ke status draft."
            );
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan saat mengubah status pindah buku ke draft.', 500);
        }
    }

    public function set_posted(Request $request, $tenant_id, $id)
    {
        $tenant = request()->attributes->get('tenant');

        $journal = Journal::cashTransfer()->where('id', '=', $id)->first();
        if (!$journal) {
            return $this->responseError('Pindah buku yang akan diubah tidak ditemukan', 404);
        }
        if ($journal->status == 'posted') {
            return $this->responseError('Pindah buku sudah dalam status posted.', 422);
        }

        // periksa total debit dan credit harus sama
        $total_debit = $journal->lines()->sum('debit');
        $total_credit = $journal->lines()->sum('credit');
        if ($total_debit != $total_credit) {
            return $this->responseError('Total debit dan kredit pada pindah buku tidak seimbang.', 422);
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
                "Berhasil memposting pindah buku."
            );
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan saat memposting pindah buku.', 500);
        }
    }

    public function destroy(Request $request, $tenant_id, $id)
    {
        $tenant = request()->attributes->get('tenant');

        $journal = Journal::cashTransfer()->where('id', '=', $id)->first();
        if (!$journal) {
            return $this->responseError('Pindah buku yang akan dihapus tidak ditemukan', 404);
        }
        if ($journal->status == 'posted') {
            return $this->responseError('Pindah buku yang sudah diposting tidak bisa dihapus', 422);
        }

        DB::beginTransaction();
        try {
            // delete journal
            $journal->delete();

            DB::commit();
            return $this->responseSuccess(
                [],
                "Berhasil menghapus pindah buku."
            );
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan saat menghapus pindah buku.', 500);
        }
    }
}
