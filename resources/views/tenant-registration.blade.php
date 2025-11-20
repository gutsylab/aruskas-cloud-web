<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Your Business - ArusKAS Cloud</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-2xl w-full mx-4">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Register Your Business</h1>
                <p class="text-gray-600">Get started with ArusKAS Cloud in minutes</p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('tenant.register.store') }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Company Information -->
                    <div class="md:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Company Information</h3>
                    </div>

                    <!-- Company Name -->
                    <div class="md:col-span-2">
                        <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Company Name *
                        </label>
                        <input type="text"
                               id="company_name"
                               name="company_name"
                               value="{{ old('company_name') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('company_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Admin Information -->
                    <div class="md:col-span-2 mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Administrator Information</h3>
                    </div>

                    <!-- Admin Name -->
                    <div>
                        <label for="admin_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Full Name *
                        </label>
                        <input type="text"
                               id="admin_name"
                               name="admin_name"
                               value="{{ old('admin_name') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('admin_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Admin Email -->
                    <div>
                        <label for="admin_email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address *
                        </label>
                        <input type="email"
                               id="admin_email"
                               name="admin_email"
                               value="{{ old('admin_email') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('admin_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password *
                        </label>
                        <input type="password"
                               id="password"
                               name="password"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm Password *
                        </label>
                        <input type="password"
                               id="password_confirmation"
                               name="password_confirmation"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="md:col-span-2 mt-6">
                        <div class="flex items-start">
                            <input type="checkbox"
                                   id="terms"
                                   name="terms"
                                   value="1"
                                   class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                   {{ old('terms') ? 'checked' : '' }}>
                            <label for="terms" class="ml-2 text-sm text-gray-700">
                                I agree to the <a href="#" class="text-blue-600 hover:underline">Terms of Service</a>
                                and <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a> *
                            </label>
                        </div>
                        @error('terms')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- General Error -->
                    @error('general')
                        <div class="md:col-span-2 bg-red-50 border border-red-200 rounded-md p-4">
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        </div>
                    @enderror

                    <!-- Submit Button -->
                    <div class="md:col-span-2 mt-6">
                        <button type="submit"
                                class="w-full bg-blue-600 text-white py-3 px-4 rounded-md font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                            Create My Business Account
                        </button>
                    </div>
                </div>
            </form>

            <!-- Login Link -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Already have a business account? Contact support to access your tenant.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
