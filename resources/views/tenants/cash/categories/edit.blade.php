<<<<<<< HEAD
@extends('partials.layouts.master')

@section('css')
    <!-- Picker CSS -->
    <link rel="stylesheet" href="{{ asset('assets/libs/air-datepicker/air-datepicker.css') }}">
    <!-- Choices css -->
    <link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/leaflet/leaflet.css') }}">
@endsection
@section('content')
    @php
        $id = 0;
        $code = old('code');
        $name = old('name');
        $deleted_at = null;

        $type = request()->get('type', 'in');
        if ($account) {
            $id = $account->id;
            $code = $account->code ?? '';
            $name = $account->name ?? '';
            $deleted_at = $account->deleted_at;
        }
    @endphp
    <!-- Begin page -->
    <div id="layout-wrapper">
        <div class="row">
            <div class="col-md-6">
                <form method="POST" id="form-edit" class="form-horizontal"
                    action="{{ route('cash-categories.update', [
                        'tenant_id' => $tenant->tenant_id,
                        'cash_category' => $id,
                    ]) }}"
                    autocomplete="off" onsubmit="return false">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" value="{{ $id }}" />
                    <input type="hidden" name="type" value="{{ $type }}" />

                    @if ($id > 0)
                        @if ($account->journalLines->count() > 0)
                            <div class="alert alert-warning px-4 pb-0 pt-4 mb-4">
                                <h5><i class="ri-error-warning-line"></i> Perhatian!</h5>
                                <p>Kategori ini sudah memiliki transaksi, proses hapus akan dibatalkan.</p>
                            </div>
                        @endif
                    @endif

                    <div class="card">
                        <span></span>
                        <!-- Order Details Section -->
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ $title }}</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    @if ($id > 0)
                                        @if ($deleted_at == null)
                                            <button type="button" onclick="confirmArchive('{{ $id }}')"
                                                class="btn btn-light-danger"><i class="ri-delete-bin-line"></i>
                                                Arsip</button>
                                        @else
                                            <button type="button" onclick="confirmActivate('{{ $id }}')"
                                                class="btn btn-light-success"><i class="ri-check-fill"></i>
                                                Aktifkan</button>

                                            <button type="button" onclick="confirmDelete('{{ $id }}')"
                                                class="btn btn-light-danger"><i class="ri-delete-bin-line"></i>
                                                Hapus</button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body">

                            @error('header')
                                <div class="alert alert-warning px-4 pb-0 pt-4 mb-4">
                                    <h5>Oops!</h5>
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror

                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-4">
                                        <label class="form-label">Nama</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            name="name" value="{{ $name }}">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>



                    <div class="d-flex justify-content-end gap-3 my-5">
                        <a href="{{ route('cash-categories.index', ['tenant_id' => $tenant->tenant_id]) }}"
                            class="btn btn-light-light text-muted"><i class="ri-close-line"></i> Batalkan</a>
                        <button type="button" id="btn-submit" onclick="doSubmit('form-edit')" class="btn btn-primary">
                            <i class="ri-save-line"></i>
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            <span id="btn-text">Simpan Kategori</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>
        <!-- Submit Section -->
    </div>
    </main>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <!-- Leaf js -->
    <script src="{{ asset('assets/libs/leaflet/leaflet.js') }}"></script>
    <!-- Select js -->
    <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    <!-- Datepicker Js -->
    <script src="{{ asset('assets/libs/air-datepicker/air-datepicker.js') }}"></script>
    <!-- File js -->
    <!-- App js -->
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/js/app/airpicker.init.js') }}"></script>
    <script src="{{ asset('assets/js/app/choices.init.js') }}"></script>

    <script>
        $(document).ready(function() {
            singleDatePicker('.single-datepicker');
            singleChoiceSelect('.form-select');
        });

        function confirmArchive(id) {
            confirmAlert(
                'Arsip Kategori Kas',
                'Apakah anda yakin ingin mengarsipkan kategori kas ini?',
                'warning',
                function() {
                    window.location.href =
                        '{{ route('cash-categories.archive', ['tenant_id' => $tenant->tenant_id, 'cash_category' => ':id']) }}'
                        .replace(':id', id);
                }
            );
        }

        function confirmActivate(id) {
            confirmAlert(
                'Aktifkan Kategori Kas',
                'Apakah anda yakin ingin mengaktifkan kategori kas ini?',
                'question',
                function() {
                    window.location.href =
                        '{{ route('cash-categories.active', ['tenant_id' => $tenant->tenant_id, 'cash_category' => ':id']) }}'
                        .replace(':id', id);
                }
            );
        }

        function confirmDelete(id) {
            confirmAlert(
                'Hapus Kategori Kas',
                'Apakah anda yakin ingin menghapus kategori kas ini? Data yang sudah dihapus tidak dapat dikembalikan lagi.',
                'warning',
                function() {
                    $.ajax({
                        url: '{{ route('cash-categories.destroy', ['tenant_id' => $tenant->tenant_id, 'cash_category' => ':id']) }}'
                            .replace(':id', id),
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            console.log(response);

                            if (response.status == 'error') {
                                errorAlert(response.message);
                                return;
                            }

                            successAlert(response.message, function() {
                                window.location.href =
                                    '{{ route('cash-categories.index', ['tenant_id' => $tenant->tenant_id]) }}';
                            });
                        },
                        error: function(xhr) {
                            errorAlert(
                                `Gagal menghapus kategori kas. Error : ${xhr.status} ${xhr.statusText}`);
                        }
                    });
                });
        }
    </script>
@endsection
=======
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
>>>>>>> origin/main
