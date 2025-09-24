<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\CashCategoryRequest;
use App\Models\Tenant\CashCategory;
use Illuminate\Http\Request;

class CashCategoryController extends Controller
{
    /**
     * Get common data for all views
     */
    protected function getCommonViewData($additionalData = [])
    {
        $tenant = request()->attributes->get('tenant');

        $commonData = [
            'activeMenu' => 'cash',
            'activeSubMenu' => 'cash.categories',
            'tenant' => $tenant,
        ];

        return array_merge($commonData, $additionalData);
    }

    /**
     * Helper method to return view with common data
     */
    protected function viewWithCommonData($view, $data = [])
    {
        return view($view, $this->getCommonViewData($data));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->viewWithCommonData('tenants.cash.categories.index', [
            'types' => CashCategory::types(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return $this->viewWithCommonData('tenants.cash.categories.create', [
            'types' => CashCategory::types(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CashCategoryRequest $request)
    {
        try {
            $category = CashCategory::create($request->validated());
            $tenant = request()->attributes->get('tenant');

            return redirect()
                ->route('cash.categories.index', ['tenant_id' => $tenant->tenant_id])
                ->with('success', 'Cash category created successfully');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['general' => 'Failed to create cash category: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CashCategory $cashCategory)
    {
        $cashCategory->load(['createdBy', 'updatedBy']);

        return $this->viewWithCommonData('tenants.cash.categories.show', [
            'cashCategory' => $cashCategory,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CashCategory $cashCategory)
    {
        $types = ['income' => 'Income', 'expense' => 'Expense'];

        return $this->viewWithCommonData('tenants.cash.categories.edit', [
            'cashCategory' => $cashCategory,
            'types' => $types,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CashCategoryRequest $request, CashCategory $cashCategory)
    {
        try {
            $cashCategory->update($request->validated());
            $tenant = request()->attributes->get('tenant');

            return redirect()
                ->route('cash.categories.index', ['tenant_id' => $tenant->tenant_id])
                ->with('success', 'Cash category updated successfully');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['general' => 'Failed to update cash category: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CashCategory $cashCategory)
    {
        try {
            $cashCategory->delete();
            $tenant = request()->attributes->get('tenant');

            return redirect()
                ->route('cash.categories.index', ['tenant_id' => $tenant->tenant_id])
                ->with('success', 'Cash category deleted successfully');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to delete cash category: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft deleted cash category.
     */
    public function restore($tenant_id, $id)
    {
        try {
            $category = CashCategory::withTrashed()->findOrFail($id);
            $category->restore();
            $tenant = request()->attributes->get('tenant');

            return redirect()
                ->route('cash.categories.trashed', ['tenant_id' => $tenant->tenant_id])
                ->with('success', 'Cash category restored successfully');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to restore cash category: ' . $e->getMessage());
        }
    }

    /**
     * Get categories by type.
     */
    public function getByType($tenant_id, $type)
    {
        if (!in_array($type, ['income', 'expense'])) {
            abort(400, 'Invalid category type');
        }

        $categories = CashCategory::where('type', $type)
            ->orderBy('name')
            ->paginate(15);

        return $this->viewWithCommonData('tenants.cash.categories.by-type', [
            'categories' => $categories,
            'type' => $type,
        ]);
    }

    /**
     * Get trashed categories.
     */
    public function trashed()
    {
        $categories = CashCategory::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(15);

        return $this->viewWithCommonData('tenants.cash.categories.trashed', [
            'categories' => $categories,
        ]);
    }

    /**
     * Force delete a category.
     */
    public function forceDelete($tenant_id, $id)
    {
        try {
            $category = CashCategory::withTrashed()->findOrFail($id);
            $category->forceDelete();
            $tenant = request()->attributes->get('tenant');

            return redirect()
                ->route('cash.categories.trashed', ['tenant_id' => $tenant->tenant_id])
                ->with('success', 'Cash category permanently deleted');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to permanently delete cash category: ' . $e->getMessage());
        }
    }
}
