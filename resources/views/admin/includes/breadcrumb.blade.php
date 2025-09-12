@php
    $segments = request()->segments(); 
    $url = '';
    $breadcrumb = [];

    // Handle /admin as "Dashboard"
    if (count($segments) === 1 && $segments[0] === 'admin') {
        $breadcrumb[] = ['label' => 'Dashboard', 'url' => null];
    } else {
        // Always start with Dashboard
        $breadcrumb[] = ['label' => 'Dashboard', 'url' => url('/admin/dashboard')];

        // Remove 'admin'
        if (!empty($segments) && $segments[0] === 'admin') {
            array_shift($segments);
        }

        // Build rest of breadcrumb
        foreach ($segments as $segment) {
            $url .= '/' . $segment;
            $breadcrumb[] = [
                'label' => ucfirst(str_replace('-', ' ', $segment)),
                'url' => url('/admin/dashboard' . $url),
            ];
        }
    }
@endphp

<nav class="breadcrumb px-4 py-2 mb-2 bg-white shadow-sm flex items-center space-x-2">
    @foreach ($breadcrumb as $index => $item)
        @if ($loop->last || !$item['url'])
            <span class="current capitalize">{{ $item['label'] }}</span>
        @else
            <a href="{{ $item['url'] }}" class="hover:underline capitalize">{{ $item['label'] }}</a>
            <span>&gt;</span>
        @endif
    @endforeach

    <span class="ml-2 timestamp" id="timestamp"></span>
</nav>
