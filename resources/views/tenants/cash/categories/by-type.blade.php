<x-tenants-layout :tenant="$tenant">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ ucfirst($type) }} Categories
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('cash.categories.create', ['tenant_id' => $tenant->tenant_id]) }}" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Add New Category
                </a>
                <a href="{{ route('cash.categories.index', ['tenant_id' => $tenant->tenant_id]) }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    All Categories
                </a>
            </div>
        </div>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <!-- Type Filter Tabs -->
            <div class="mb-6 flex space-x-4">
                <a href="{{ route('cash.categories.by-type', ['tenant_id' => $tenant->tenant_id, 'type' => 'income']) }}" 
                   class="px-4 py-2 rounded-lg font-medium {{ $type === 'income' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Income Categories
                </a>
                <a href="{{ route('cash.categories.by-type', ['tenant_id' => $tenant->tenant_id, 'type' => 'expense']) }}" 
                   class="px-4 py-2 rounded-lg font-medium {{ $type === 'expense' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Expense Categories
                </a>
            </div>

            @if($categories->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Name
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Description
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Created
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($categories as $category)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full text-sm font-medium 
                                                           {{ $category->type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $category->type === 'income' ? '↗' : '↙' }}
                                                </span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $category->name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs">
                                            {{ $category->description ?: '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $category->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('cash.categories.show', ['tenant_id' => $tenant->tenant_id, 'cashCategory' => $category]) }}" 
                                               class="text-indigo-600 hover:text-indigo-900">View</a>
                                            <a href="{{ route('cash.categories.edit', ['tenant_id' => $tenant->tenant_id, 'cashCategory' => $category]) }}" 
                                               class="text-blue-600 hover:text-blue-900">Edit</a>
                                            <form method="POST" 
                                                  action="{{ route('cash.categories.destroy', ['tenant_id' => $tenant->tenant_id, 'cashCategory' => $category]) }}" 
                                                  class="inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this category?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $categories->links() }}
                </div>

                <!-- Summary -->
                <div class="mt-6 bg-{{ $type === 'income' ? 'green' : 'red' }}-50 border border-{{ $type === 'income' ? 'green' : 'red' }}-200 rounded-lg p-4">
                    <h3 class="text-lg font-medium text-{{ $type === 'income' ? 'green' : 'red' }}-800 mb-2">
                        {{ ucfirst($type) }} Categories Summary
                    </h3>
                    <p class="text-sm text-{{ $type === 'income' ? 'green' : 'red' }}-700">
                        You have <strong>{{ $categories->total() }}</strong> {{ $type }} categories in total.
                        @if($type === 'income')
                            These categories help you track money coming into your business.
                        @else
                            These categories help you track money going out of your business.
                        @endif
                    </p>
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-gray-500 text-lg mb-4">No {{ $type }} categories found</div>
                    <p class="text-gray-400 mb-4">
                        @if($type === 'income')
                            Create income categories to track money coming into your business.
                        @else
                            Create expense categories to track money going out of your business.
                        @endif
                    </p>
                    <a href="{{ route('cash.categories.create', ['tenant_id' => $tenant->tenant_id]) }}" 
                       class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Create {{ ucfirst($type) }} Category
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-tenants-layout>
