@extends('layouts.tenant', ['tenant' => $tenant])

@section('title', 'Kategori Kas - ' . $tenant->name)
@section('page-title', 'Kategori Kas')

@push('styles')
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard', ['tenant_id' => $tenant->tenant_id]) }}">Dashboard</a></li>
    <li class="breadcrumb-item">Kas</li>
    <li class="breadcrumb-item active">Kategori Kas</li>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-xl-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body pt-4 pb-3">
                        <div class="row align-items-center">
                            <div class="col-sm-6">
                                <h4 class="card-title mb-1">Kategori Kas</h4>
                            </div>
                            <div class="col-sm-6 text-end">
                                <a href="{{ route('cash.categories.create', $tenant->tenant_id) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus me-2"></i>Tambah
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table mb-0" id="datatable">
                                <thead class="table-lightx">
                                    <tr>
                                        <th width="50">#</th>
                                        <th>Nama</th>
                                        <th>Jenis</th>
                                        <th>Deskripsi</th>
                                        <th>Dibuat</th>
                                        <th width="120">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable if needed
            if ($('#datatable').length > 0) {
                initDatatables('#datatable',
                    '{{ route('cash.categories.datatables', ['tenant_id' => $tenant->tenant_id]) }}', [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'type',
                            name: 'type'
                        },
                        {
                            data: 'description',
                            name: 'description'
                        },
                        {
                            data: 'created_at_since',
                            name: 'created_at_since'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ]);
            }

            // Search functionality
            $('#search').on('keyup', function() {
                if (typeof categoriesTable !== 'undefined') {
                    categoriesTable.search(this.value).draw();
                }
            });

            // Filter functionality
            $('#type-filter, #status-filter').on('change', function() {
                // You can implement real-time filtering here
                // Or submit the form for server-side filtering
            });
        });
    </script>
@endsection
