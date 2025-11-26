@php
    $user = auth()->user();
@endphp

@if (is_null($user->email_verified_at))
    <div class="alert alert-warning w-100 p-4 mb-0 mt-4" role="alert">
        <h5><i class="ri-error-warning-line"></i> Verifikasi Email Anda</h5>
        <p class="mb-0">
            Silakan verifikasi alamat email Anda untuk mengakses semua fitur. Cek inbox email Anda untuk
            tautan
            verifikasi. Jika belum menerima email, klik
            <a href="javascript:void(0);" onclick="doResendVerificationEmail()">di sini</a> untuk
            mengirim ulang.
        </p>
    </div>
@endif
<div class="d-flex align-items-center mt-2 mb-2 py-2">
    {{-- <h6 class="mb-0 flex-grow-1">{{ $title ?? '' }}</h6> --}}
    <div class="flex-shrink-0">
        @php
            if (!isset($breadcrumbs) || count($breadcrumbs) == 0) {
                $breadcrumbs = [
                    [
                        'label' => '<i class="ri-dashboard-line pe-nav-icon"></i> Dashboard',
                        'url' => route('dashboard', ['tenant_id' => $tenant->tenant_id]),
                    ],
                ];
            } else {
                // insert dashboard link at the beginning
                array_unshift($breadcrumbs, [
                    'label' => '<i class="ri-dashboard-line pe-nav-icon"></i> Dashboard',
                    'url' => route('dashboard', ['tenant_id' => $tenant->tenant_id]),
                ]);
            }
        @endphp
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-arrow justify-content-end mb-0 colored-breadcrumb bg-light">
                {{-- <li class="breadcrumb-item"><a href="javascript:void(0)">@yield('title-sub')</a></li>
                <li class="breadcrumb-item active" aria-current="page">@yield('pagetitle')</li> --}}

                @php
                    $count = count($breadcrumbs);
                @endphp
                @forelse ($breadcrumbs as $index => $breadcrumb)
                    @php
                        $label = $breadcrumb['label'];
                        $url = $breadcrumb['url'] ?? 'javascript:void(0)';
                        $last = $index === $count - 1;
                    @endphp
                    <li class="breadcrumb-item {{ $last ? 'active' : '' }}" aria-current="{{ $last ? 'page' : '' }}">
                        <a href="{{ $url }}">
                            {!! $label !!}
                        </a>
                    </li>
                @empty
                @endforelse
            </ol>
        </nav>
    </div>
</div>
