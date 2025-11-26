<?php

namespace App\Http\Controllers;

class BaseController extends Controller
{

    protected function response(string $view, array $data = [], string $message = "Success", int $status = 200)
    {

        if (request()->expectsJson()) {
            $success = $status >= 200 && $status < 300;

            if (!$success) {
                return response()->json([
                    'success' => $success,
                    'message' => $message,
                ], $status);
            }
            return response()->json([
                'success' => $success,
                'message' => $message,
                'data' => $data
            ], $status);
        }

        return view($view, $data);
    }
<<<<<<< HEAD

    protected function view(string $view,  string $title = '', array $groupMenu = [], array $data = [], array $breadcrumbs = [])
    {
        return view(
            $view,
            [
                'scrollspy' => 0,
                'simplePage' => 0,
                'title' => $title,
                ...$data,
                ...$groupMenu,
                'breadcrumbs' => $breadcrumbs,
            ]
        );
    }

    protected function viewTenant(string $view,  string $title = '', array $groupMenu = [], array $data = [], array $breadcrumbs = [])
    {
        $tenant = request()->attributes->get('tenant');

        $final_breadcrumbs = [];
        foreach ($breadcrumbs as $breadcrumb) {
            $key = array_key_first($breadcrumb);
            $value = $breadcrumb[$key] ?? 'javascript:void(0)';
            $final_breadcrumbs[] = [
                'label' => $key,
                'url' => $value,
            ];
        }

        return view(
            'tenants.' . $view,
            [
                'title' => $title,
                'tenant' => $tenant,
                ...$data,
                ...$groupMenu,
                'breadcrumbs' => $final_breadcrumbs,
            ]
        );
    }

    protected function viewTenantAuth($data = [])
    {

        return view(
            'tenants.auth',
            $data
        );
    }
=======
>>>>>>> origin/main
}
