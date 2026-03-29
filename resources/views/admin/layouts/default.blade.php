<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $sidebarSettings = \App\Models\Setting::whereIn('key', ['shop_name'])->pluck('value', 'key');
        $storeName = $sidebarSettings['shop_name'] ?: config('app.name', 'Store Name');
        $brandSettings = \App\Models\Setting::whereIn('key', [
            'branding_color',
            'secondary_color',
            'accent_color',
        ])->pluck('value', 'key');
        $primaryColor = $brandSettings['branding_color'] ?? '#2563eb';
        $secondaryColor = $brandSettings['secondary_color'] ?? '#64748b';
        $accentColor = $brandSettings['accent_color'] ?? '#f59e0b';
    @endphp
    <title>{{ $storeName }} Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --color-primary: {{ $primaryColor }};
            --color-secondary: {{ $secondaryColor }};
            --color-accent: {{ $accentColor }};
        }
    </style>
    <style>
        /* Smooth hover transitions and modern scrollbar */
        nav a:hover,
        button:hover {
            transition: all 0.3s ease;
        }

        /* Brand color helpers (admin) */
        .bg-primary { background-color: var(--color-primary); }
        .text-primary { color: var(--color-primary); }
        .border-primary { border-color: var(--color-primary); }
        .hover\:bg-primary:hover { background-color: var(--color-primary); }
        .hover\:text-primary:hover { color: var(--color-primary); }
        .ring-primary { --tw-ring-color: var(--color-primary); }
        .bg-secondary { background-color: var(--color-secondary); }
        .text-secondary { color: var(--color-secondary); }
        .border-secondary { border-color: var(--color-secondary); }
        .bg-accent { background-color: var(--color-accent); }
        .text-accent { color: var(--color-accent); }
        .border-accent { border-color: var(--color-accent); }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: #6b7280;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #4b5563;
        }

        /* Sidebar transition */
        aside {
            transition: transform 0.3s ease;
        }

        .sidebar-collapsed {
            transform: translateX(-100%);
        }

        .sidebar-collapsed+main {
            margin-left: 0;
        }

        /* Floating toggle button */
        #toggle-sidebar-floating {
            display: none;
        }

        .show-floating {
            display: block !important;
        }

        /* Breadcrumb styles */
        .breadcrumb { 
            display: flex; 
            align-items: center; 
            gap: 0.5rem; 
            font-size: 0.75rem; 
            color: #4b5563; 
        }
        .breadcrumb a { 
            color: #2563eb; 
            transition: color 0.2s ease; 
        }
        .breadcrumb a:hover { 
            color: #1e40af; 
        }
        .breadcrumb span { 
            color: #6b7280; 
        }
        .breadcrumb .current { 
            font-weight: 600; 
            color: #1f2937; 
        }
        button.bg-blue-50,
        button.bg-blue-100,
        button.bg-blue-200,
        button.bg-blue-300,
        button.bg-blue-400,
        button.bg-blue-500,
        button.bg-blue-600,
        button.bg-blue-700,
        button.bg-blue-800,
        button.bg-blue-900 {
            background-color: var(--color-primary) !important;
            color: #fff !important;
        }
        button.text-blue-50,
        button.text-blue-100,
        button.text-blue-200,
        button.text-blue-300,
        button.text-blue-400,
        button.text-blue-500,
        button.text-blue-600,
        button.text-blue-700,
        button.text-blue-800,
        button.text-blue-900 {
            color: var(--color-primary) !important;
        }
        button.border-blue-50,
        button.border-blue-100,
        button.border-blue-200,
        button.border-blue-300,
        button.border-blue-400,
        button.border-blue-500,
        button.border-blue-600,
        button.border-blue-700,
        button.border-blue-800,
        button.border-blue-900 {
            border-color: var(--color-primary) !important;
        }
        button.hover\:bg-blue-50:hover,
        button.hover\:bg-blue-100:hover,
        button.hover\:bg-blue-200:hover,
        button.hover\:bg-blue-300:hover,
        button.hover\:bg-blue-400:hover,
        button.hover\:bg-blue-500:hover,
        button.hover\:bg-blue-600:hover,
        button.hover\:bg-blue-700:hover,
        button.hover\:bg-blue-800:hover,
        button.hover\:bg-blue-900:hover {
            background-color: var(--color-primary) !important;
            color: #fff !important;
        }
        button.hover\:text-blue-50:hover,
        button.hover\:text-blue-100:hover,
        button.hover\:text-blue-200:hover,
        button.hover\:text-blue-300:hover,
        button.hover\:text-blue-400:hover,
        button.hover\:text-blue-500:hover,
        button.hover\:text-blue-600:hover,
        button.hover\:text-blue-700:hover,
        button.hover\:text-blue-800:hover,
        button.hover\:text-blue-900:hover {
            color: var(--color-primary-700) !important;
        }
        button.focus\:border-blue-50:focus,
        button.focus\:border-blue-100:focus,
        button.focus\:border-blue-200:focus,
        button.focus\:border-blue-300:focus,
        button.focus\:border-blue-400:focus,
        button.focus\:border-blue-500:focus,
        button.focus\:border-blue-600:focus,
        button.focus\:border-blue-700:focus,
        button.focus\:border-blue-800:focus,
        button.focus\:border-blue-900:focus {
            border-color: var(--color-primary) !important;
        }
        button.focus\:ring-blue-50:focus,
        button.focus\:ring-blue-100:focus,
        button.focus\:ring-blue-200:focus,
        button.focus\:ring-blue-300:focus,
        button.focus\:ring-blue-400:focus,
        button.focus\:ring-blue-500:focus,
        button.focus\:ring-blue-600:focus,
        button.focus\:ring-blue-700:focus,
        button.focus\:ring-blue-800:focus,
        button.focus\:ring-blue-900:focus {
            --tw-ring-color: var(--color-primary) !important;
        }
        .timestamp {
            font-size: 0.7rem;
            color: #6b7280;
        }

        /* Custom styles for enhanced form appearance */
        .form-container {
            transition: all 0.3s ease;
        }

        .form-container:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .input-field,
        .textarea-field,
        .select-field {
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .input-field:focus,
        .textarea-field:focus,
        .select-field:focus {
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .error-message {
            display: block;
            margin-top: 0.25rem;
            font-size: 0.75rem;
            color: #dc2626;
        }

        .form-button {
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .form-button:hover {
            transform: translateY(-1px);
        }

        .form-button:active {
            transform: translateY(0);
        }

        .checkbox-field {
            accent-color: #2563eb;
        }

        .table-field {
            border: 1px solid #e5e7eb;
        }

        .table-field th,
        .table-field td {
            padding: 0.5rem;
        }

        .table-field input {
            padding: 0.25rem;
        }

        .attribute-display {
            background-color: #f9fafb;
            border-radius: 0.5rem;
            padding: 0.5rem;
        }

        /* Table list style */
        .table-container {
            transition: all 0.3s ease;
        }
        .table-container:hover {
            box-shadow: none;
        }
        .table-field {
            border: 1px solid #e5e7eb;
        }
        .table-field th, .table-field td {
            padding: 0.75rem;
        }
        .table-field tr:hover {
            background-color: #f9fafb;
        }
        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 500;
        }
        select.form-input {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
        }
    </style>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
</head>

<body class="bg-gray-50 font-sans antialiased flex flex-col min-h-screen">
    <div id="admin-toast" class="fixed top-4 left-1/2 -translate-x-1/2 z-50 hidden">
        <div class="bg-primary text-white text-xs font-semibold px-4 py-2 rounded-lg shadow-lg">
            <span id="admin-toast-text">Copied to clipboard</span>
        </div>
    </div>
    <!-- Main Dashboard Layout -->
    <div class="flex flex-1 overflow-hidden">
        <!-- Sidebar -->
        @include('admin.includes.sidebar')

        <!-- Main Content -->
        <main class="flex-1 p-4 overflow-y-auto ml-60">
            <!-- Header -->
            @include('admin.includes.header')

            @yield('content')

        </main>
    </div>

    <!-- Footer -->
    @include('admin.includes.footer')

    <!-- JavaScript for Sidebar Toggle -->
    <script>
        function showAdminToast(message) {
            const toast = document.getElementById('admin-toast');
            const text = document.getElementById('admin-toast-text');
            if (!toast || !text) return;
            text.textContent = message || 'Done';
            toast.classList.remove('hidden');
            clearTimeout(window.__adminToastTimer);
            window.__adminToastTimer = setTimeout(() => {
                toast.classList.add('hidden');
            }, 1600);
        }

        const sidebar = document.getElementById('sidebar');
        const toggleButton = document.getElementById('toggle-sidebar');
        const toggleButtonFloating = document.getElementById('toggle-sidebar-floating');
        const mainContent = document.querySelector('main');
        const breadcrumbIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 6h10M5 12h14M5 18h8"></path>';

        function toggleSidebar() {
            sidebar.classList.toggle('sidebar-collapsed');
            toggleButtonFloating.classList.toggle('show-floating');
            const isCollapsed = sidebar.classList.contains('sidebar-collapsed');
            if (sidebar.classList.contains('sidebar-collapsed')) {
                mainContent.classList.remove('ml-60');
            } else {
                mainContent.classList.add('ml-60');
            }
            if (toggleButton) {
                toggleButton.querySelector('svg').innerHTML = breadcrumbIcon;
            }
            if (toggleButtonFloating) {
                toggleButtonFloating.querySelector('svg').innerHTML = breadcrumbIcon;
            }
        }

        toggleButton.addEventListener('click', toggleSidebar);
        toggleButtonFloating.addEventListener('click', toggleSidebar);
    </script>
</body>

</html>
