@if ($errors->any())
<div class="bg-red-100 text-red-700 px-3 py-2 rounded mb-4 text-sm">
  <ul class="list-disc list-inside">
    @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif

@if (session()->has('failed'))
<div class="flex items-center my-2 p-4 bg-red-100 rounded-lg dark:bg-red-200" role="alert">
  <i class="fas fa-triangle-exclamation flex-shrink-0 text-red-700"></i>
  <div class="ml-3 text-sm font-medium text-red-700 dark:text-red-800">
    {{ session()->get('failed') }}
  </div>
</div>
@endif

@if (session()->has('success'))
<div class="flex items-center my-2 p-4 bg-green-100 rounded-lg dark:bg-green-200" role="alert">
  <i class="fas fa-check-circle flex-shrink-0 text-green-700"></i>
  <div class="ml-3 text-sm font-medium text-green-700 dark:text-green-800">
    {{ session()->get('success') }}
  </div>
</div>
@endif
