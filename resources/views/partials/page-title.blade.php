<div class="d-flex align-items-center mt-2 mb-2 py-2">
    {{-- <h6 class="mb-0 flex-grow-1">{{ $title ?? '' }}</h6> --}}
    <div class="flex-shrink-0">
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
                            {{ $label }}
                        </a>
                    </li>
                @empty
                @endforelse
            </ol>
        </nav>
    </div>
</div>
