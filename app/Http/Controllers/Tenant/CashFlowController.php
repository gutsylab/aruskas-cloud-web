<?php

namespace App\Http\Controllers\Tenant;

use Illuminate\Http\Request;
use App\Models\Tenant\Journal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController;
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
            'activeMenu' => 'cash',
            'activeSubMenu' => 'cash-flows',
        ];
    }
    public function index()
    {
        //
        return $this->view(
            'tenants.cash_flow.index',
            $this->title,
            $this->groupMenu(),
            [],
            [
                ['name' => 'Arus Kas']
            ]
        );
    }

    public function dataTable(Request $request)
    {
        $cashFlows = Journal::select([
            'id',
            'code',
            'date',
            'description',
            'reference',
            'status',
            'posted_at',
        ])
            ->with(['lines.account'])
            ->withSum('lines', 'debit');

        return DataTables::of($cashFlows)
            ->addIndexColumn()
            ->editColumn('code', function ($row) {
                if (!userCan('view', User::class)) return $row->email;
                return '<a href="' . route('admin.users.show', $row) . '" class="text-decoration-underline text-primary">' . $row->email . '</a>';
            })
            ->addColumn('roles',  function (User $row) {
                return $row->roles->map(function ($role) {
                    return '<span class="badge bg-primary small">' . $role->name . '</span>';
                })->implode(' ');
            })
            ->filterColumn('roles', function ($query, $keyword) {
                $query->whereHas('roles', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->editColumn('email_verify_at',  function ($row) {
                if ($row->email_verified_at == null) {
                    return '<span class="badge bg-warning">Belum Verifikasi</span>';
                }
                return Carbon::parse($row->email_verified_at)->format('d M Y H:i:s');
            })
            ->editColumn('last_login_at',  function ($row) {
                if ($row->last_login_at == null) {
                    return '<span class="badge bg-warning">Belum Pernah Login</span>';
                }
                return Carbon::parse($row->last_login_at)->format('d M Y H:i:s');
            })
            ->addColumn('created_at_since',  function ($row) {
                return Carbon::parse($row->created_at)->since();
            })
            ->addColumn('updated_at_since',  function ($row) {
                return Carbon::parse($row->updated_at)->since();
            })
            ->rawColumns([
                'email',
                'name',
                'roles',
                'email_verify_at',
                'last_login_at',
                'created_at_since',
                'updated_at_since',
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
