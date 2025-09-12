@if (session()->has('message'))
    <div class="mb-4 p-2 bg-green-100 text-green-700 rounded-md text-sm">
        {{ session('message') }}
    </div>
@endif

@if (session()->has('failed'))
    <div class="mb-4 p-2 bg-red-100 text-red-700 rounded-md text-sm">
        {{ session('failed') }}
    </div>
@endif
