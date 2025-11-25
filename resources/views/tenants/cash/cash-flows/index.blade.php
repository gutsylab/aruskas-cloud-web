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
            <div class="col-12 mb-4">
                <div class="accordion accordion-icon accordion-primary accordion-border-box" id="demo_accordion_main_03">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#demo_accordion_item_31" aria-expanded="false"
                                aria-controls="demo_accordion_item_31">
                                <i class="bi bi-funnel-fill me-2"></i> Filter
                            </button>
                        </h2>
                        <div id="demo_accordion_item_31" class="accordion-collapse collapse"
                            data-bs-parent="#demo_accordion_main_03">
                            <div class="accordion-body py-5">
                                <div class="row g-4">
                                    <div class="col-md-4 col-xl">
                                        <select id="status-status" class="form-select">
                                            <option value="draft">Draft</option>
                                            <option value="posted">Diposting</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 col-xl">
                                        <input type="text" class="form-control" id="human-friendly-picker"
                                            placeholder="Pilih Tanggal">
                                    </div>
                                    <div class="col-xl d-flex justify-content-end align-items-center gap-2">
                                        <button class="btn btn-light-primary" type="button"><i
                                                class="ri-equalizer-line me-2"></i>Tambah Filter</button>
                                        <button class="btn btn-light-danger" type="button">Hapus Filter</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Daftar Arus Kas</h6>
                        <div class="row">
                            <div class="col-md-12">
                                <a href="{{ route('cash-flows.create', ['tenant_id' => $tenant->tenant_id, 'type' => 'in']) }}"
                                    class="btn btn-success"><i class="bi bi-plus-lg me-1"></i>Kas Masuk</a>
                                <a href="{{ route('cash-flows.create', ['tenant_id' => $tenant->tenant_id, 'type' => 'out']) }}"
                                    class="btn btn-danger"><i class="bi bi-plus-lg me-1"></i>Kas Keluar</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table table-nowrap table-striped table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>No. Kas</th>
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                    <th>Total</th>
                                    <th>Status</th>
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
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#datatable').DataTable({
                scrollX: false,
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{!! route('cash-flows.dt', ['tenant_id' => $tenant->tenant_id]) !!}',
                    data: function(d) {
                        // d.project_id = $('#project_id').val();
                        // d.cost_type_id = $('#cost_type_id').val();

                        // // Menambahkan parameter date range
                        // var dateRange = $('#date_range').val();
                        // if (dateRange) {
                        //     var dates = dateRange.split(" to ");
                        //     d.start_date = dates[0];
                        //     d.end_date = dates.length > 1 ? dates[1] : dates[0];
                        // }
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
                    [2, 'desc']
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
                        name: 'code'
                    },
                    {
                        data: 'date',
                        name: 'date',
                    },
                    {
                        data: 'description',
                        name: 'description',
                    },
                    {
                        data: 'lines_sum_debit',
                        name: 'lines_sum_debit',
                        className: 'text-end'
                    },
                    {
                        data: 'status',
                        name: 'status',
                    },

                ]
            });

            let status = document.getElementById('status-status');
            if (status) {
                const status = new Choices('#status-status', {
                    placeholderValue: 'Pilih Status',
                    searchPlaceholderValue: 'Cari...',
                    removeItemButton: true,
                    itemSelectText: 'Tekan untuk memilih',
                });
            }
        });
    </script>
@endsection
