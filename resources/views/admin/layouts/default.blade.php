<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-commerce Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Smooth hover transitions and modern scrollbar */
        nav a:hover,
        button:hover {
            transition: all 0.3s ease;
        }

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
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
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
    <!-- Main Dashboard Layout -->
    <div class="flex flex-1 overflow-hidden">
        <!-- Sidebar -->
        @include('admin.includes.sidebar')

        <!-- Main Content -->
        <main class="flex-1 p-4 overflow-y-auto ml-56">
            <!-- Header -->
            @include('admin.includes.header')

            @yield('content')

        </main>
    </div>

    <!-- Footer -->
    @include('admin.includes.footer')

    <!-- JavaScript for Sidebar Toggle -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const toggleButton = document.getElementById('toggle-sidebar');
        const toggleButtonFloating = document.getElementById('toggle-sidebar-floating');
        const mainContent = document.querySelector('main');

        function toggleSidebar() {
            sidebar.classList.toggle('sidebar-collapsed');
            toggleButtonFloating.classList.toggle('show-floating');
            if (sidebar.classList.contains('sidebar-collapsed')) {
                mainContent.classList.remove('ml-56');
                toggleButton.querySelector('svg').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6h8M6 12h8M6 18h8M16 6l4 6-4 6"></path>';
            } else {
                mainContent.classList.add('ml-56');
                toggleButton.querySelector('svg').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5l-4 7 4 7M20 6H9M20 12H9M20 18H9"></path>';
            }
        }

        toggleButton.addEventListener('click', toggleSidebar);
        toggleButtonFloating.addEventListener('click', toggleSidebar);
    </script>
</body>

</html>
