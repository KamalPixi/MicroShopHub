@if ($errors->any())
    <div class="bg-red-100 text-red-700 px-3 py-2 rounded mb-4 text-sm">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
