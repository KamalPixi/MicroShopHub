@if(($sectionAlert['section'] ?? '') === $section && !empty($sectionAlert['message']))
    <div class="mt-3 rounded-lg border px-3 py-2 text-xs font-medium {{ ($sectionAlert['type'] ?? 'success') === 'error' ? 'border-red-200 bg-red-50 text-red-700' : 'border-green-200 bg-green-50 text-green-700' }}">
        {{ $sectionAlert['message'] }}
    </div>
@endif
