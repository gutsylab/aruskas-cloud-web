<?php

namespace App\Http\Controllers\Global;

use App\Http\Controllers\Controller;
use App\Models\Global\Merchant;
use App\Mail\TenantEmailVerification;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EmailVerificationController extends Controller
{
    protected TenantService $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }
    /**
     * Verify the tenant's email address.
     */
    public function verify(Request $request, $id, $hash)
    {
        // Find the merchant
        $merchant = Merchant::findOrFail($id);

        // Check if the hash matches
        if (!hash_equals((string) $hash, sha1($merchant->email))) {
            abort(403, 'Invalid verification link.');
        }

        // Check if email is already verified
        if ($merchant->email_verified_at) {
            return view('tenant-email-verified', [
                'merchant' => $merchant,
                'message' => 'Email sudah terverifikasi sebelumnya.',
                'status' => 'already_verified'
            ]);
        }

        // Verify the signature
        if (!$request->hasValidSignature()) {
            abort(403, 'Link verifikasi tidak valid atau sudah kedaluwarsa.');
        }

        // Mark email as verified
        $merchant->update([
            'email_verified_at' => now(),
        ]);

        // Also verify the tenant user's email
        $this->tenantService->setTenantConnection($merchant);
        
        $tenantUser = \App\Models\Tenant\User::where('email', $merchant->email)->first();
        if ($tenantUser && !$tenantUser->email_verified_at) {
            $tenantUser->update([
                'email_verified_at' => now(),
            ]);
        }

        $this->tenantService->resetToGlobalConnection();

        return view('tenant-email-verified', [
            'merchant' => $merchant,
            'message' => 'Email berhasil diverifikasi! Anda sekarang dapat menggunakan semua fitur aplikasi.',
            'status' => 'verified',
            'tenantUrl' => url("/{$merchant->tenant_id}"),
        ]);
    }

    /**
     * Resend verification email.
     */
    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $merchant = Merchant::where('email', $request->email)
            ->whereNull('email_verified_at')
            ->first();

        if (!$merchant) {
            return back()->withErrors([
                'email' => 'Email tidak ditemukan atau sudah terverifikasi.'
            ]);
        }

        // Send verification email
        Mail::to($merchant->email)->send(new TenantEmailVerification($merchant));

        return back()->with('status', 'Link verifikasi email telah dikirim ulang ke ' . $merchant->email);
    }

    /**
     * Show resend verification form.
     */
    public function showResendForm()
    {
        return view('tenant-email-resend');
    }
}
