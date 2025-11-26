<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8" />

    @php
        $page_title = config('app.name');
        if (isset($title) && !empty($title)) {
            $page_title = $title . ' | ' . config('app.name');
        }
    @endphp

    <title>{{ $page_title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta content="Aplikasi POS Cloud Murah dan Mudah Digunakan untuk UMKM" name="description" />
    <meta content="{{ config('app.organization_name') }}" name="author" />

    <!-- layout setup -->
    <script type="module" src="{{ asset('assets/js/layout-setup.js') }}"></script>

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/k_favicon_32x.png') }}">

    @yield('css')
    @include('partials.head-css')
</head>

<body>

    @include('partials.header')
    @include('partials.sidebar')
    @include('partials.horizontal')

    <main class="app-wrapper">
        <div class="container-fluid">

            @include('partials.page-title')


            @yield('content')
            @include('partials.switcher')
            @include('partials.scroll-to-top')
            @include('partials.footer')

            @include('partials.vendor-scripts')

            @yield('js')
            {{-- BAGIAN UNTUK MENAMPILKAN NOTIFIKASI --}}
            @if (session('success'))
                <script>
                    try {
                        if (typeof Swal !== 'undefined') {
                            successAlert('{{ session('success') }}');
                        } else {
                            alert('Success: {{ session('success') }}');
                        }
                    } catch (error) {
                        alert('Success: {{ session('success') }}');
                    }
                </script>
            @endif

            @if (session('error'))
                <script>
                    try {
                        if (typeof Swal !== 'undefined') {
                            errorAlert('{{ session('error') }}');
                        } else {
                            alert('Error: {{ session('error') }}');
                        }
                    } catch (error) {
                        alert('Error: {{ session('error') }}');
                    }
                </script>
            @endif

            <script>
                function doSubmit(formId) {
                    const btn = document.getElementById('btn-submit');
                    const btnText = document.getElementById('btn-text');
                    const spinner = btn.querySelector('.spinner-border');
                    const icon = btn.querySelector('i');

                    // Disable button
                    btn.disabled = true;

                    // Hide icon, show spinner
                    icon.classList.add('d-none');
                    spinner.classList.remove('d-none');
                    btnText.textContent = 'Menyimpan...';

                    // Submit form
                    const form = document.getElementById(formId);
                    form.submit();
                }

                function doLogout() {
                    confirmAlert('Konfirmasi Logout', 'Anda yakin ingin keluar ?', 'question', function() {
                        // Create a form element
                        var form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route('logout') }}';

                        // Add CSRF token input
                        var csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = '{{ csrf_token() }}';
                        form.appendChild(csrfInput);

                        // Append the form to the body and submit it
                        document.body.appendChild(form);
                        form.submit();
                    });
                }

                @if (!auth()->user()->hasVerifiedEmail())
                    function doResendVerificationEmail() {
                        // Create a form element
                        var form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route('resend.verification.email', ['tenant_id' => $tenant->tenant_id]) }}';

                        // Add CSRF token input
                        var csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = '{{ csrf_token() }}';
                        form.appendChild(csrfInput);

                        // Append the form to the body and submit it
                        document.body.appendChild(form);
                        form.submit();
                    }
                @endif
            </script>
        </div>
    </main>




</body>

</html>
