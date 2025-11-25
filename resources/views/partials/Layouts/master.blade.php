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
        </div>
    </main>

</body>

</html>
