@php
    $segments = request()->segments();
    $url = '';
    $breadcrumb = [];

    if (count($segments) === 1 && $segments[0] === 'admin') {
        $breadcrumb[] = ['label' => 'Dashboard', 'url' => null];
    } else {
        $breadcrumb[] = ['label' => 'Dashboard', 'url' => url('/admin/dashboard')];
        if (!empty($segments) && $segments[0] === 'admin') {
            array_shift($segments);
        }
        foreach ($segments as $segment) {
            $url .= '/' . $segment;
            $breadcrumb[] = [
                'label' => ucfirst(str_replace('-', ' ', $segment)),
                'url' => url('/admin/dashboard' . $url),
            ];
        }
    }
@endphp

<header class="bg-white border border-gray-200 shadow-xs px-3 py-2 mb-2 rounded-lg flex justify-between items-center gap-3">
    <div class="flex items-center gap-2 min-w-0">
        <!-- Floating Toggle Button (Visible when sidebar is collapsed) -->
        <button id="toggle-sidebar-floating" class="text-gray-700 hover:text-gray-900 hover:bg-gray-100 border border-gray-200 rounded-md p-1 z-10" aria-label="Expand sidebar">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 6h10M5 12h14M5 18h8"></path>
            </svg>
        </button>

        <svg class="w-4 h-4 text-gray-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2M9 19"></path>
        </svg>

        <nav class="flex items-center gap-1.5 text-xs text-gray-500 truncate" aria-label="Breadcrumb">
            @foreach ($breadcrumb as $item)
                @if ($loop->last || !$item['url'])
                    <span class="font-semibold text-gray-800 capitalize truncate">{{ $item['label'] }}</span>
                @else
                    <a href="{{ $item['url'] }}" class="hover:text-gray-900 hover:underline capitalize">{{ $item['label'] }}</a>
                    <span class="text-gray-400">/</span>
                @endif
            @endforeach
        </nav>
    </div>
    <div class="flex items-center shrink-0">
        <span class="text-gray-600 text-xs mr-2.5">Admin</span>
        <form action="{{ route('admin.logout') }}" method="POST" class="inline-flex">
            @csrf
            <button type="submit" class="text-blue-600 hover:underline text-xs flex items-center">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7"></path>
                </svg>
                Signout
            </button>
        </form>
    </div>
</header>
