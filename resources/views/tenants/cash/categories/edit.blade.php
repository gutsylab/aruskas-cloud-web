<x-tenants-layout :tenant="$tenant">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Cash Category
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('cash.categories.show', ['tenant_id' => $tenant->tenant_id, 'cashCategory' => $cashCategory]) }}" 
                   class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    View Category
                </a>
                <a href="{{ route('cash.categories.index', ['tenant_id' => $tenant->tenant_id]) }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Categories
                </a>
            </div>
        </div>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <form method="POST" action="{{ route('cash.categories.update', ['tenant_id' => $tenant->tenant_id, 'cashCategory' => $cashCategory]) }}">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Category Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $cashCategory->name) }}" 
                           required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror"
                           placeholder="Enter category name">
                    @error('name')
                        <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Type -->
                <div class="mb-4">
                    <label for="type" class="block text-sm font-medium text-gray-700">
                        Category Type <span class="text-red-500">*</span>
                    </label>
                    <select id="type" 
                            name="type" 
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('type') border-red-500 @enderror">
                        <option value="">Select category type</option>
                        @foreach($types as $value => $label)
                            <option value="{{ $value }}" {{ old('type', $cashCategory->type) === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('type')
                        <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700">
                        Description
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('description') border-red-500 @enderror"
                              placeholder="Enter category description (optional)">{{ old('description', $cashCategory->description) }}</textarea>
                    @error('description')
                        <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                    @enderror
                    <div class="mt-1 text-sm text-gray-500">
                        Optional. Provide a brief description of this category.
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        <span class="text-red-500">*</span> Required fields
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('cash.categories.show', ['tenant_id' => $tenant->tenant_id, 'cashCategory' => $cashCategory]) }}" 
                           class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Update Category
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Current Values Info -->
    <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
        <h3 class="text-lg font-medium text-gray-800 mb-2">Current Values</h3>
        <div class="grid md:grid-cols-3 gap-4 text-sm">
            <div>
                <span class="font-medium text-gray-600">Name:</span>
                <span class="ml-2">{{ $cashCategory->name }}</span>
            </div>
            <div>
                <span class="font-medium text-gray-600">Type:</span>
                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                           {{ $cashCategory->type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ ucfirst($cashCategory->type) }}
                </span>
            </div>
            <div>
                <span class="font-medium text-gray-600">Description:</span>
                <span class="ml-2">{{ $cashCategory->description ?: 'None' }}</span>
            </div>
        </div>
    </div>

    <!-- Help Section -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="text-lg font-medium text-blue-800 mb-2">Category Types</h3>
        <div class="grid md:grid-cols-2 gap-4 text-sm">
            <div>
                <h4 class="font-medium text-green-700 mb-1">Income Categories</h4>
                <p class="text-gray-600">
                    Use for money coming into your business, such as sales revenue, service fees, or other earnings.
                </p>
            </div>
            <div>
                <h4 class="font-medium text-red-700 mb-1">Expense Categories</h4>
                <p class="text-gray-600">
                    Use for money going out of your business, such as office supplies, rent, utilities, or other costs.
                </p>
            </div>
        </div>
    </div>
</x-tenants-layout>
