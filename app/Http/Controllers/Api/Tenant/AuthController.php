<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle tenant login via API.
     *
     * @group Tenant Authentication
     * @bodyParam email string required The user's email address. Example: admin@example.com
     * @bodyParam password string required The user's password. Example: password123
     * @bodyParam device_name string optional Device name for token identification. Example: iPhone 12
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Login successful",
     *   "data": {
     *     "user": {
     *       "id": 1,
     *       "name": "John Doe",
     *       "email": "admin@example.com"
     *     },
     *     "tenant": {
     *       "id": 1,
     *       "name": "ABC Company",
     *       "tenant_id": "TNT123456"
     *     },
     *     "token": "1|abcdef123456..."
     *   }
     * }
     *
     * @response 401 {
     *   "success": false,
     *   "message": "These credentials do not match our records."
     * }
     *
     * @response 404 {
     *   "success": false,
     *   "message": "Tenant not found"
     * }
     */
    public function login(Request $request)
    {
        $tenant = request()->attributes->get('tenant');

        if (!$tenant) {
            return response()->json([
                'success' => false,
<<<<<<< HEAD
                'message' => 'Akun tidak ditemukan'
=======
                'message' => 'Tenant not found'
>>>>>>> origin/main
            ], 404);
        }

        // Validate input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'nullable|string|max:255',
        ]);

        // Set tenant connection
        $connectionName = "tenant_{$tenant->tenant_id}";

        // Find user in tenant database
        $user = \App\Models\Tenant\User::on($connectionName)
            ->where('email', $request->email)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
<<<<<<< HEAD
                'message' => 'Email atau password yang Anda masukkan tidak sesuai dengan data kami.'
=======
                'message' => 'These credentials do not match our records.'
>>>>>>> origin/main
            ], 401);
        }

        // Check if email is verified
        if (!$user->email_verified_at) {
            return response()->json([
                'success' => false,
<<<<<<< HEAD
                'message' => 'Silakan verifikasi alamat email Anda sebelum login.'
=======
                'message' => 'Please verify your email address before logging in.'
>>>>>>> origin/main
            ], 403);
        }

        // Set the connection for the user model
        $user->setConnection($connectionName);

        // Create token without ID prefix
        $deviceName = $request->device_name ?? $request->userAgent() ?? 'api-client';
        $token = \App\Models\Tenant\PersonalAccessToken::createTokenWithoutPrefix(
            $user,
            $deviceName
        )->plainTextToken;

        return response()->json([
            'success' => true,
<<<<<<< HEAD
            'message' => 'Login berhasil',
=======
            'message' => 'Login successful',
>>>>>>> origin/main
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at,
                ],
                'tenant' => [
                    'id' => $tenant->id,
                    'name' => $tenant->name,
                    'tenant_id' => $tenant->tenant_id,
                    'slug' => $tenant->slug,
                ],
                'token' => $token,
            ]
        ], 200);
    }

<<<<<<< HEAD
=======
    /**
     * Get authenticated user information.
     *
     * @group Tenant Authentication
     * @authenticated
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "user": {
     *       "id": 1,
     *       "name": "John Doe",
     *       "email": "admin@example.com"
     *     },
     *     "tenant": {
     *       "id": 1,
     *       "name": "ABC Company",
     *       "tenant_id": "TNT123456"
     *     }
     *   }
     * }
     *
     * @response 401 {
     *   "success": false,
     *   "message": "Unauthenticated"
     * }
     */
>>>>>>> origin/main
    public function me(Request $request)
    {
        $tenant = request()->attributes->get('tenant');

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $request->user(),
                'tenant' => $tenant,
            ]
        ]);
    }

<<<<<<< HEAD
=======
    /**
     * Handle tenant logout via API.
     *
     * @group Tenant Authentication
     * @authenticated
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Logout successful"
     * }
     *
     * @response 401 {
     *   "success": false,
     *   "message": "Unauthenticated"
     * }
     */
>>>>>>> origin/main
    public function logout(Request $request)
    {
        // Revoke the current token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
<<<<<<< HEAD
            'message' => 'Logout berhasil'
=======
            'message' => 'Logout successful'
>>>>>>> origin/main
        ]);
    }

    /**
     * Revoke all user tokens (logout from all devices).
     *
     * @group Tenant Authentication
     * @authenticated
     *
     * @response 200 {
     *   "success": true,
     *   "message": "All sessions revoked successfully"
     * }
     */
    public function logoutAll(Request $request)
    {
        // Revoke all tokens for the user
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
<<<<<<< HEAD
            'message' => 'Semua sesi berhasil dicabut'
=======
            'message' => 'All sessions revoked successfully'
>>>>>>> origin/main
        ]);
    }

    /**
     * Refresh authentication token.
     *
     * @group Tenant Authentication
     * @authenticated
     * @bodyParam device_name string optional New device name for token. Example: iPhone 12
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Token refreshed successfully",
     *   "data": {
     *     "token": "2|newtoken123456..."
     *   }
     * }
     */
    public function refresh(Request $request)
    {
        $request->validate([
            'device_name' => 'nullable|string|max:255',
        ]);

        // Revoke current token
        $request->user()->currentAccessToken()->delete();

        // Create new token without ID prefix
        $deviceName = $request->device_name ?? $request->userAgent() ?? 'api-client';
        $token = \App\Models\Tenant\PersonalAccessToken::createTokenWithoutPrefix(
            $request->user(),
            $deviceName
        )->plainTextToken;

        return response()->json([
            'success' => true,
<<<<<<< HEAD
            'message' => 'Token berhasil diperbarui',
=======
            'message' => 'Token refreshed successfully',
>>>>>>> origin/main
            'data' => [
                'token' => $token,
            ]
        ]);
    }
}
