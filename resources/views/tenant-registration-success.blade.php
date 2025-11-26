<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful - ArusKAS Cloud</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-2xl w-full mx-4">
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <!-- Success Icon -->
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-6">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <!-- Success Message -->
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Welcome to ArusKAS Cloud!</h1>
            <p class="text-lg text-gray-600 mb-4">Your business account has been created successfully.</p>
            
            <!-- Email Verification Notice -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-8">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-yellow-800">Email Verification Required</h3>
                        <p class="text-sm text-yellow-700 mt-1">
                            We've sent a verification email to <strong>{{ $adminEmail }}</strong>. 
                            Please check your inbox and click the verification link to activate your account.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Account Details -->
            <div class="bg-gray-50 rounded-lg p-6 mb-8 text-left">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Details</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Company:</span>
                        <span class="font-medium">{{ $merchant->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tenant ID:</span>
                        <span class="font-mono text-sm bg-gray-200 px-2 py-1 rounded">{{ $merchant->tenant_id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Admin Email:</span>
                        <span class="font-medium">{{ $adminEmail }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Plan:</span>
                        <span class="font-medium">{{ $plan->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Your Store URL:</span>
                        <span class="font-medium text-blue-600">{{ $tenantUrl }}</span>
                    </div>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-blue-900 mb-4">Next Steps</h3>
                <div class="text-left space-y-3 text-blue-800">
                    <p><strong>1. Verify your email:</strong> Check your inbox for verification email and click the link</p>
                    <p><strong>2. Access your store:</strong> After verification, visit <a href="{{ $tenantUrl }}" class="underline">{{ $tenantUrl }}</a></p>
                    <p><strong>3. Login with your credentials:</strong> Use your email (<strong>{{ $adminEmail }}</strong>) and password to sign in</p>
                    <p><strong>4. Setup your store:</strong> Configure your products and settings</p>
                    <p><strong>5. Start selling:</strong> Your POS system is ready to use!</p>
                </div>
            </div>

            @if($plan->trial_days > 0)
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-8">
                    <p class="text-green-800">
                        <strong>Free Trial:</strong> You have {{ $plan->trial_days }} days to explore all features at no cost.
                    </p>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="space-y-4">
                <a href="{{ route('tenant.email.resend.form') }}" 
                   class="inline-block w-full bg-blue-600 text-white py-3 px-6 rounded-md font-semibold hover:bg-blue-700 transition duration-200">
                    Didn't receive email? Resend Verification
                </a>
                
                <a href="{{ $tenantUrl }}" 
                   class="inline-block w-full border border-gray-300 text-gray-700 py-3 px-6 rounded-md font-semibold hover:bg-gray-50 transition duration-200">
                    Go to Store (after verification)
                </a>
                
                <a href="/" 
                   class="inline-block w-full border border-gray-300 text-gray-700 py-3 px-6 rounded-md font-semibold hover:bg-gray-50 transition duration-200">
                    Back to Homepage
                </a>
            </div>

            <!-- Support Info -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-600">
                    Need help getting started? 
                    <a href="mailto:support@aruskas.com" class="text-blue-600 hover:underline">Contact our support team</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
