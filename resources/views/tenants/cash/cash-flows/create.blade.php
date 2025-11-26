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
                <form method="POST" id="form-create" class="form-horizontal"
                    action="{{ route('cash-flows.store', ['tenant_id' => $tenant->tenant_id]) }}" autocomplete="off"
                    onsubmit="return false">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}" />
                    <div class="card">
                        <span></span>
                        <!-- Order Details Section -->
                        <div class="card-header">
                            <h5 class="mb-0">{{ $title }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group mb-4">
                                        <label class="form-label">Tanggal</label>
                                        <input type="text"
                                            class="form-control single-datepicker @error('date') is-invalid @enderror"
                                            placeholder="Pilih tanggal" name="date" value="{{ old('date') }}">
                                        @error('date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-4">
                                        <label for="account_id"
                                            class="form-label">{{ $type == 'in' ? 'Masuk ke' : 'Keluar dari' }}</label>
                                        <select id="account_id" name="account_id"
                                            class="form-select @error('account_id') is-invalid @enderror" required>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}"
                                                    {{ old('account_id') == $account->id ? 'selected' : '' }}>
                                                    {{ $account->code }} - {{ $account->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('account_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label class="form-label">Referensi</label>
                                        <input type="text" class="form-control @error('reference') is-invalid @enderror"
                                            placeholder="(Opsional)" name="reference" value="{{ old('reference') }}">
                                        @error('reference')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group mb-4">
                                        <label class="form-label">Deskripsi</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" rows="2" name="description"
                                            placeholder="Deskripsi transaksi (opsional)">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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
                                    @if ($errors->has('lines') || $errors->has('lines.*'))
                                        <div class="alert alert-danger mb-3">
                                            <ul class="mb-0">
                                                @if ($errors->has('lines'))
                                                    <li>{{ $errors->first('lines') }}</li>
                                                @endif
                                                @foreach ($errors->get('lines.*.*') as $messages)
                                                    @foreach ($messages as $message)
                                                        <li>{{ $message }}</li>
                                                    @endforeach
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <table class="table table-bordered" id="table-lines">
                                        <thead>
                                            <tr>
                                                <th class="bg-info text-light">Kategori</th>
                                                <th class="bg-info text-light">Keterangan</th>
                                                <th class="text-end bg-info text-light">Jumlah</th>
                                                <th class="bg-info text-light" style="width: 100px">Aksi</th>
                                            </tr>
                                        </thead>

                                        <tbody>

                                        </tbody>

                                        <tfoot>
                                            <tr class="bg-light">
                                                <th colspan="2" class="text-end">Total:</th>
                                                <th class="text-end" id="total-amount">0</th>
                                                <th></th>
                                            </tr>
                                            <tr>
                                                <td class="p-2" style="width: 350px">
                                                    <select name="category_id" id="new-category" class="form-select">
                                                        @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}">
                                                                {{ $category->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" id="new-description"
                                                        placeholder="Keterangan" />
                                                </td>
                                                <td style="width: 250px">
                                                    <input type="text"
                                                        class="form-control text-end form-control-currency" id="new-amount"
                                                        placeholder="Jumlah" />
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-success" onclick="addNewLine()"
                                                        id="add-cash-flow-detail"><i class="bi bi-plus-lg"></i></button>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3 my-5">
                        <a href="{{ route('cash-flows.index', ['tenant_id' => $tenant->tenant_id]) }}"
                            class="btn btn-light-light text-muted"><i class="ri-close-line"></i> Batalkan</a>
                        <button type="button" id="btn-submit" onclick="doSubmit('form-create')"
                            class="btn btn-primary">
                            <i class="ri-save-line"></i>
                            <span class="spinner-border spinner-border-sm d-none" role="status"
                                aria-hidden="true"></span>
                            <span id="btn-text">Simpan Arus Kas Baru</span></button>
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
        let lineIndex = 0;

        $(document).ready(function() {
            singleDatePicker('.single-datepicker');
            singleChoiceSelect('.form-select');
        });

        function addNewLine() {
            const categorySelect = document.getElementById('new-category');
            const categoryId = categorySelect.value;
            const categoryName = categorySelect.options[categorySelect.selectedIndex].text;
            const description = document.getElementById('new-description').value;
            const amount = parseFloat(document.getElementById('new-amount').value) || 0;

            // Validasi
            if (!categoryId) {
                alert('Pilih kategori terlebih dahulu');
                return;
            }
            if (!description.trim()) {
                alert('Masukkan keterangan');
                return;
            }
            if (amount <= 0) {
                alert('Jumlah harus lebih besar dari 0');
                return;
            }

            // Tambahkan baris ke tbody
            const tbody = document.querySelector('#table-lines tbody');
            const row = document.createElement('tr');
            row.dataset.index = lineIndex;
            row.innerHTML = `
                <td>
                    <input type="hidden" name="lines[${lineIndex}][category_id]" value="${categoryId}">
                    ${categoryName}
                </td>
                <td>
                    <input type="hidden" name="lines[${lineIndex}][description]" value="${description}">
                    <span class="line-description">${description}</span>
                </td>
                <td class="text-end">
                    <input type="hidden" name="lines[${lineIndex}][amount]" value="${amount}" class="line-amount-input">
                    <span class="line-amount">${formatCurrency(amount)}</span>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-warning me-1" onclick="editLine(${lineIndex})">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteLine(${lineIndex})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;

            tbody.appendChild(row);
            lineIndex++;

            // Reset input
            document.getElementById('new-description').value = '';
            document.getElementById('new-amount').value = '';
            document.getElementById('new-description').focus();

            // Hitung ulang total
            calculateTotal();
        }

        function editLine(index) {
            const row = document.querySelector(`tr[data-index="${index}"]`);
            const categoryInput = row.querySelector('input[name*="category_id"]');
            const descriptionInput = row.querySelector('input[name*="description"]');
            const amountInput = row.querySelector('input[name*="amount"]');

            const categoryId = categoryInput.value;
            const description = descriptionInput.value;
            const amount = amountInput.value;

            // Ubah baris menjadi form editable
            row.innerHTML = `
                <td>
                    <select name="lines[${index}][category_id]" class="form-select edit-category">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" ${categoryId == '{{ $category->id }}' ? 'selected' : ''}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="text" name="lines[${index}][description]" class="form-control edit-description" value="${description}">
                </td>
                <td>
                    <input type="text" name="lines[${index}][amount]" class="form-control text-end edit-amount line-amount-input form-control-currency" value="${amount}">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-success me-1" onclick="saveLine(${index})">
                        <i class="bi bi-check-lg"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="cancelEdit(${index})">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </td>
            `;

            // Store original data untuk cancel
            row.dataset.originalCategory = categoryId;
            row.dataset.originalDescription = description;
            row.dataset.originalAmount = amount;
        }

        function saveLine(index) {
            const row = document.querySelector(`tr[data-index="${index}"]`);
            const categorySelect = row.querySelector('.edit-category');
            const categoryId = categorySelect.value;
            const categoryName = categorySelect.options[categorySelect.selectedIndex].text;
            const description = row.querySelector('.edit-description').value;
            const amount = parseFloat(row.querySelector('.edit-amount').value) || 0;

            // Validasi
            if (!description.trim()) {
                alert('Masukkan keterangan');
                return;
            }
            if (amount <= 0) {
                alert('Jumlah harus lebih besar dari 0');
                return;
            }

            // Kembalikan ke tampilan normal
            row.innerHTML = `
                <td>
                    <input type="hidden" name="lines[${index}][category_id]" value="${categoryId}">
                    ${categoryName}
                </td>
                <td>
                    <input type="hidden" name="lines[${index}][description]" value="${description}">
                    <span class="line-description">${description}</span>
                </td>
                <td class="text-end">
                    <input type="hidden" name="lines[${index}][amount]" value="${amount}" class="line-amount-input">
                    <span class="line-amount">${formatCurrency(amount)}</span>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-warning me-1" onclick="editLine(${index})">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteLine(${index})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;

            // Hitung ulang total
            calculateTotal();
        }

        function cancelEdit(index) {
            const row = document.querySelector(`tr[data-index="${index}"]`);
            const categoryId = row.dataset.originalCategory;
            const description = row.dataset.originalDescription;
            const amount = row.dataset.originalAmount;

            // Cari nama kategori
            const categorySelect = document.getElementById('new-category');
            const option = Array.from(categorySelect.options).find(opt => opt.value == categoryId);
            const categoryName = option ? option.text : '';

            // Kembalikan ke tampilan semula
            row.innerHTML = `
                <td>
                    <input type="hidden" name="lines[${index}][category_id]" value="${categoryId}">
                    ${categoryName}
                </td>
                <td>
                    <input type="hidden" name="lines[${index}][description]" value="${description}">
                    <span class="line-description">${description}</span>
                </td>
                <td class="text-end">
                    <input type="hidden" name="lines[${index}][amount]" value="${amount}" class="line-amount-input">
                    <span class="line-amount">${formatCurrency(amount)}</span>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-warning me-1" onclick="editLine(${index})">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteLine(${index})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
        }

        function deleteLine(index) {
            if (confirm('Hapus baris ini?')) {
                const row = document.querySelector(`tr[data-index="${index}"]`);
                row.remove();
                calculateTotal();
            }
        }

        function calculateTotal() {
            const amounts = document.querySelectorAll('.line-amount-input');
            let total = 0;

            amounts.forEach(input => {
                total += parseFloat(input.value) || 0;
            });

            document.getElementById('total-amount').textContent = formatCurrency(total);
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'decimal',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(amount);
        }

        // Event listener untuk Enter key pada input
        document.addEventListener('DOMContentLoaded', function() {
            // Prevent form submission on Enter key for all inputs except submit button
            const form = document.querySelector('form');
            form.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && e.target.type !== 'submit') {
                    e.preventDefault();
                }
            });

            document.getElementById('new-amount').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    addNewLine();
                }
            });

            document.getElementById('new-description').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('new-amount').focus();
                }
            });
        });

        function validateForm(event) {
            // Allow form submission only when submit button is clicked
            return true;
        }
    </script>
@endsection
