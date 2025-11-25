@extends('partials.layouts.master')

@section('css')
    <!-- Picker CSS -->
    <link rel="stylesheet" href="{{ asset('assets/libs/air-datepicker/air-datepicker.css') }}">
    <!-- Choices css -->
    <link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/leaflet/leaflet.css') }}">
@endsection
@section('content')
    <!-- Begin page -->
    <div id="layout-wrapper">
        <div class="row">
            <div class="col-md-12">


                @if ($cashFlow->status == 'posted')
                    <div class="alert alert-secondary px-4 pb-0 pt-4 mb-4">
                        <h5><i class="ri-information-line"></i> Info</h5>
                        <p>Arus kas ini berstatus <strong>Posted</strong>. Diposting pada
                            <strong>{{ \Carbon\Carbon::parse($cashFlow->posted_at)->format('d M Y H:i') }}</strong>
                        </p>
                    </div>
                @endif

                <div class="card">
                    <!-- Order Details Section -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('cash-flows.index', ['tenant_id' => $tenant->tenant_id]) }}"
                                class="btn btn-light-secondary"><i class="ri-arrow-left-line"></i></a>

                            @php
                                $status = $cashFlow->status ?? '';

                                if ($status == 'posted') {
                                    $status = '<span class="badge bg-primary rounded-pill">Diposting</span>';
                                } elseif ($status == 'draft') {
                                    $status = '<span class="badge bg-secondary rounded-pill">Draft</span>';
                                } else {
                                    $status = '';
                                }
                            @endphp
                            <h5 class="mb-0 ms-4">{{ $title }} {!! $status !!}</h5>
                        </div>

                        <a href="{{ route('cash-flows.edit', ['tenant_id' => $tenant->tenant_id, 'cash_flow' => $cashFlow->id]) }}"
                            class="btn btn-light-primary"><i class="ri-pencil-line me-1"></i>Ubah</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group mb-4">
                                    <label class="form-label"><strong>Tanggal</strong></label>
                                    <p>{{ \Carbon\Carbon::parse($cashFlow->date)->format('d M Y') }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-4">
                                    <label for="account_id"
                                        class="form-label"><strong>{{ $type == 'in' ? 'Masuk ke' : 'Keluar dari' }}</strong></label>
                                    <p>{{ $account->code }} - {{ $account->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="form-label"><strong>Referensi</strong></label>
                                    <p>{{ $cashFlow->reference }}</p>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-4">
                                    <label class="form-label"><strong>Deskripsi</strong></label>
                                    <p>{{ $cashFlow->description ?? '' }}</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card">

                    <div class="card-header">
                        <h5 class="mb-0">Detail Arus Kas </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 table-responsivex">

                                <table class="table table-bordered" id="table-lines">
                                    <thead>
                                        <tr>
                                            <th class="bg-info text-light">Kategori</th>
                                            <th class="bg-info text-light">Keterangan</th>
                                            <th class="text-end bg-info text-light">Jumlah</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @php
                                            $total = 0;
                                        @endphp
                                        @foreach ($lines as $line)
                                            @php
                                                $amount = $type == 'in' ? $line->credit : $line->debit;

                                                $total += $amount;
                                            @endphp
                                            <tr>
                                                <td>{{ $line->account->name }}</td>
                                                <td>{{ $line->description }}</td>
                                                <td class="text-end">{{ convertCurrency($amount, true, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>

                                    <tfoot>
                                        <tr class="bg-light">
                                            <th colspan="2" class="text-end">Total:</th>
                                            <th class="text-end" id="total-amount">
                                                {{ convertCurrency($total, true, 2) }}
                                            </th>
                                        </tr>

                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
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
@endsection
