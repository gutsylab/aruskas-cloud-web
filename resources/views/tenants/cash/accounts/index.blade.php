@extends('partials.layouts.master')

@section('css')
    <!-- Picker css -->
    <link rel="stylesheet" href="{{ asset('assets/libs/air-datepicker/air-datepicker.css') }}">
    <!-- Choices css -->
    <link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">


    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <!--datatable responsive css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
@endsection

@section('content')
    <!-- Begin page -->
    <div id="layout-wrapper">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Daftar Akun</h6>

                        <div class="d-flex align-items-center">
                            <div class="col-md-4 col-xl">
                                <select id="filter-status" class="form-select form-filter">
                                    <option value="">Semua Status</option>
                                    <option value="active">Aktif</option>
                                    <option value="archived">Arsip</option>
                                </select>
                            </div>
                            <a href="{{ route('cash-accounts.edit', ['tenant_id' => $tenant->tenant_id, 'cash_account' => 0]) }}"
                                class="btn btn-primary ms-3"><i class="bi bi-plus-lg me-1"></i>Tambah Akun</a>

                        </div>

                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table table-nowrap table-striped table-bordered w-100">
                            <thead>
                                <tr>
                                    <th style="width: 80px">No.</th>
                                    <th style="width: 100px">Kode</th>
                                    <th>Nama</th>
                                    <th style="width: 200px">Saldo</th>
                                    <th style="width:100px">Status</th>
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
    <!-- Submit Section -->
    </main>
@endsection

@section('js')
    <!-- Datepicker Js -->
    <script src="{{ asset('assets/libs/air-datepicker/air-datepicker.js') }}"></script>

    <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <!--datatable js-->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

    <script src="{{ asset('assets/js/table/datatable.init.js') }}"></script>

    <script src="{{ asset('assets/js/app/airpicker.init.js') }}"></script>
    <script src="{{ asset('assets/js/app/choices.init.js') }}"></script>

    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>

    <script>
        $(document).ready(function() {

            $('#datatable').DataTable({
                scrollX: false,
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{!! route('cash-accounts.dt', ['tenant_id' => $tenant->tenant_id, 'show_archived' => true]) !!}',
                    data: function(d) {

                        var filterStatus = $('#filter-status').val();
                        console.log(filterStatus);
                        if (filterStatus) {
                            d.status = filterStatus;
                        }
                    }
                },
                "dom": "<'dt--top-section'<'row'<'col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center'l><'col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>" +
                    "<'table-responsive'tr>" +
                    "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
                "oLanguage": {
                    "oPaginate": {
                        "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>',
                        "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>'
                    },
                    "sInfo": "Showing page _PAGE_ of _PAGES_",
                    "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                    "sSearchPlaceholder": "Search...",
                    "sLengthMenu": "Results :  _MENU_",
                },
                layout: {
                    topStart: 'buttons'
                },
                buttons: ['copy', {
                    extend: 'excel',
                    text: 'Save as Excel'
                }],
                "stripeClasses": [],
                "lengthMenu": [10, 20, 50, 100],
                "pageLength": 10,
                "order": [
                    [1, 'asc']
                ],
                "columns": [{
                        data: 'DT_RowIndex',
                        name: "DT_RowIndex",
                        orderable: false,
                        searchable: false,
                        className: 'text-end'
                    },
                    {
                        data: 'code',
                        name: 'code',
                        orderable: true
                    },
                    {
                        data: 'name',
                        name: 'name',
                        orderable: true
                    },
                    {
                        data: 'balance',
                        name: 'balance',
                        orderable: false,
                        className: 'text-end'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        className: 'text-center'
                    }

                ]
            });

            $("#filter-status").change(function() {
                $('#datatable').DataTable().ajax.reload();
            });

        });
    </script>
@endsection
