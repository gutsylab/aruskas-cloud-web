<x-tenants-layout :tenant="$tenant">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Cash Category Details
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('cash.categories.edit', ['tenant_id' => $tenant->tenant_id, 'cashCategory' => $cashCategory]) }}" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Edit Category
                </a>
                <a href="{{ route('cash.categories.index', ['tenant_id' => $tenant->tenant_id]) }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Categories
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid md:grid-cols-2 gap-6">
        <!-- Category Information -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h3 class="text-lg font-semibold mb-4">Category Information</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Name</label>
                        <div class="mt-1 text-lg font-medium text-gray-900">
                            {{ $cashCategory->name }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Type</label>
                        <div class="mt-1">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                       {{ $cashCategory->type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($cashCategory->type) }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Description</label>
                        <div class="mt-1 text-gray-900">
                            {{ $cashCategory->description ?: 'No description provided' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Audit Information -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h3 class="text-lg font-semibold mb-4">Audit Information</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Created</label>
                        <div class="mt-1">
                            <div class="text-gray-900">{{ $cashCategory->created_at->format('M d, Y \a\t H:i') }}</div>
                            @if($cashCategory->createdBy)
                                <div class="text-sm text-gray-600">by {{ $cashCategory->createdBy->name }}</div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Last Updated</label>
                        <div class="mt-1">
                            <div class="text-gray-900">{{ $cashCategory->updated_at->format('M d, Y \a\t H:i') }}</div>
                            @if($cashCategory->updatedBy)
                                <div class="text-sm text-gray-600">by {{ $cashCategory->updatedBy->name }}</div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">ID</label>
                        <div class="mt-1 text-gray-900 font-mono text-sm">
                            {{ $cashCategory->id }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Actions</h3>
            
            <div class="flex space-x-4">
                <a href="{{ route('cash.categories.edit', ['tenant_id' => $tenant->tenant_id, 'cashCategory' => $cashCategory]) }}" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Edit Category
                </a>
                
                <form method="POST" 
                      action="{{ route('cash.categories.destroy', ['tenant_id' => $tenant->tenant_id, 'cashCategory' => $cashCategory]) }}" 
                      class="inline"
                      onsubmit="return confirm('Are you sure you want to delete this category?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Delete Category
                    </button>
                </form>
            </div>
            
            <div class="mt-4 text-sm text-gray-600">
                <p><strong>Note:</strong> Deleting this category will soft delete it. You can restore it later from the trashed categories page.</p>
            </div>
        </div>
    </div>
</x-tenants-layout>
