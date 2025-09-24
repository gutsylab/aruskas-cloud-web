@extends('layouts.tenant', ['tenant' => $tenant])

@section('title', 'Kategori Kas - ' . $tenant->name)
@section('page-title', 'Kategori Kas')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard', ['tenant_id' => $tenant->tenant_id]) }}">Dashboard</a></li>
    <li class="breadcrumb-item">Kas</li>
    <li class="breadcrumb-item"><a href="{{ route('cash.categories.index', ['tenant_id' => $tenant->tenant_id]) }}">Kategori
            Kas</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-xl-12">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <h4 class="card-title mb-0">
                            <a href="{{ route('cash.categories.index', $tenant->tenant_id) }}"
                                class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                            &nbsp;
                            <span>Tambah Kategori Kas</span>
                        </h4>
                    </div>

                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="row">
            <div class="col-md-6">
                <form class="form-horizontal"
                    action="{{ route('cash.categories.store', ['tenant_id' => $tenant->tenant_id]) }}" method="POST">
                    @csrf

                    <div class="card shadow-sm border-0">

                        <div class="card-body p2">
                            <div class="form-group row ">
                                <label for="name" class="form-label col-form-label col-sm-3">Nama</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm" id="name" name="name"
                                        required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="type" class="form-label col-form-label col-sm-3">Tipe</label>
                                <div class="col-sm-9">
                                    <select class="form-select form-select-sm" id="type" name="type">
                                        <option value="">Pilih Jenis Kategori</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label for="description" class="form-label col-form-label col-sm-3">Deskripsi</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
