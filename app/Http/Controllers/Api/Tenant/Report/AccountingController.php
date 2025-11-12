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
                return $this->responseError('Invalid start date format', 400);
            }
            $start_date = request()->get('start_date');
        }
        if (request()->has('end_date')) {
            // check if end date is valid format
            if (!strtotime(request()->get('end_date'))) {
                return $this->responseError('Invalid end date format', 400);
            }
            $end_date = request()->get('end_date');
        }

        if (strtotime($start_date) > strtotime($end_date)) {
            return $this->responseError('Start date cannot be greater than end date', 400);
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
            return $this->responseError('Account is required', 400);
        }

        if (request()->has('start_date')) {
            // check if start date is valid format
            if (!strtotime(request()->get('start_date'))) {
                return $this->responseError('Invalid start date format', 400);
            }
            $start_date = request()->get('start_date');
        }
        if (request()->has('end_date')) {
            // check if end date is valid format
            if (!strtotime(request()->get('end_date'))) {
                return $this->responseError('Invalid end date format', 400);
            }
            $end_date = request()->get('end_date');
        }

        if (strtotime($start_date) > strtotime($end_date)) {
            return $this->responseError('Start date cannot be greater than end date', 400);
        }

        $account = Account::select('id', 'code', 'name')
            ->where('id', '=', $account_id)->first();
        if (!$account) {
            return $this->responseError('Account not found', 404);
        }


        try {
            $result = BankStatementService::generate($account, $start_date, $end_date);
            return $this->responseSuccess($result);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage(), 500);
        }
    }

    public function report_cash_summary()
    {
        $end_date = date("Y-m-t");

        if (request()->has('end_date')) {
            // check if end date is valid format
            if (!strtotime(request()->get('end_date'))) {
                return $this->responseError('Invalid end date format', 400);
            }
            $end_date = request()->get('end_date');
        }


        try {
            $result = CashSummaryService::generateAsOf($end_date);
            return $this->responseSuccess($result);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage(), 500);
        }
    }
}
