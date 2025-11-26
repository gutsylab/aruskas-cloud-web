<?php

namespace App\Http\Controllers\Api\Tenant\Report;

use Exception;
use Illuminate\Http\Request;
use App\Models\Tenant\Account;
use App\Services\ProfitLossService;
use App\Http\Controllers\Controller;
use App\Services\CashSummaryService;
use App\Services\BankStatementService;
use App\Http\Controllers\ApiController;
use App\Services\CashFlowService;

class AccountingController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function report_profit_loss()
    {
        $start_date = date("Y-m-01");
        $end_date = date("Y-m-t");

        if (request()->has('start_date')) {
            // check if start date is valid format
            if (!strtotime(request()->get('start_date'))) {
                return $this->responseError('Format tanggal mulai tidak valid', 400);
            }
            $start_date = request()->get('start_date');
        }
        if (request()->has('end_date')) {
            // check if end date is valid format
            if (!strtotime(request()->get('end_date'))) {
                return $this->responseError('Format tanggal akhir tidak valid', 400);
            }
            $end_date = request()->get('end_date');
        }

        if (strtotime($start_date) > strtotime($end_date)) {
            return $this->responseError('Tanggal mulai tidak boleh lebih besar dari tanggal akhir', 400);
        }


        $result = ProfitLossService::generate($start_date, $end_date);
        return $this->responseSuccess($result);
    }

    public function report_bank_statement()
    {
        $start_date = date("Y-m-01");
        $end_date = date("Y-m-t");
        $account_id = request()->get('account_id');

        if (!$account_id || $account_id <= 0) {
            return $this->responseError('Akun harus dipilih', 400);
        }

        if (request()->has('start_date')) {
            // check if start date is valid format
            if (!strtotime(request()->get('start_date'))) {
                return $this->responseError('Format tanggal mulai tidak valid', 400);
            }
            $start_date = request()->get('start_date');
        }
        if (request()->has('end_date')) {
            // check if end date is valid format
            if (!strtotime(request()->get('end_date'))) {
                return $this->responseError('Format tanggal akhir tidak valid', 400);
            }
            $end_date = request()->get('end_date');
        }

        if (strtotime($start_date) > strtotime($end_date)) {
            return $this->responseError('Tanggal mulai tidak boleh lebih besar dari tanggal akhir', 400);
        }

        $account = Account::select('id', 'code', 'name')
            ->where('id', '=', $account_id)->first();
        if (!$account) {
            return $this->responseError('Akun tidak ditemukan', 404);
        }


        try {
            $result = BankStatementService::generate($account, $start_date, $end_date);
            return $this->responseSuccess($result);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage(), 500);
        }
    }

    public function report_cash_flow_summary()
    {
        $end_date = date("Y-m-t");

        if (request()->has('end_date')) {
            // check if end date is valid format
            if (!strtotime(request()->get('end_date'))) {
                return $this->responseError('Format tanggal akhir tidak valid', 400);
            }
            $end_date = request()->get('end_date');
        }


        try {
            $result = CashFlowService::generateSummary($end_date);
            return $this->responseSuccess($result);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage(), 500);
        }
    }
    public function report_cash_flow_detail()
    {
        $start_date = date("Y-m-01");
        $end_date = date("Y-m-t");



        if (request()->has('start_date')) {
            // check if start date is valid format
            if (!strtotime(request()->get('start_date'))) {
                return $this->responseError('Format tanggal mulai tidak valid', 400);
            }
            $start_date = request()->get('start_date');
        }

        if (request()->has('end_date')) {
            // check if end date is valid format
            if (!strtotime(request()->get('end_date'))) {
                return $this->responseError('Format tanggal akhir tidak valid', 400);
            }
            $end_date = request()->get('end_date');
        }

        // end date must be greater than start date
        if (strtotime($end_date) < strtotime($start_date)) {
            return $this->responseError('Tanggal mulai harus lebih kecil dari tanggal akhir');
        }


        try {
            $result = CashFlowService::generateDetail($start_date, $end_date);
            return $this->responseSuccess($result);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage(), 500);
        }
    }
}
