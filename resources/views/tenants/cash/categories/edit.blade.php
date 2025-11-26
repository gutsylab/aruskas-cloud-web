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
