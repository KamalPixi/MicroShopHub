<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - MicroShopHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/heroicons@2.1.1/dist/heroicons.min.js"></script>
    <style>
        :root {
            --color-primary: #111111;
            --color-primary-strong: #000000;
            --color-primary-soft: #f3f4f6;
        }

        /* Smooth transitions and modern scrollbar */
        button:hover,
        input:focus {
            transition: all 0.3s ease;
        }
        .bg-primary { background-color: var(--color-primary); }
        .text-primary { color: var(--color-primary); }
        .border-primary { border-color: var(--color-primary); }
        .hover\:bg-primary:hover { background-color: var(--color-primary); }
        .hover\:text-primary:hover { color: var(--color-primary); }
        .ring-primary { --tw-ring-color: var(--color-primary); }
        [class*="bg-primary/"] {
            background-color: var(--color-primary-soft) !important;
            color: var(--color-primary-strong) !important;
        }
        [class*="text-primary/"] {
            color: var(--color-primary) !important;
        }
        [class*="border-primary/"] {
            border-color: var(--color-primary-soft) !important;
        }
        [class*="ring-primary/"] {
            --tw-ring-color: var(--color-primary-soft) !important;
        }
        [class*="hover:bg-primary/"]:hover {
            background-color: var(--color-primary) !important;
            color: #fff !important;
        }
        [class*="hover:text-primary/"]:hover {
            color: var(--color-primary) !important;
        }
        [class*="focus:border-primary/"]:focus {
            border-color: var(--color-primary) !important;
        }
        [class*="focus:ring-primary/"]:focus {
            --tw-ring-color: var(--color-primary) !important;
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
    </style>
</head>

<body class="bg-gray-50 font-sans antialiased flex items-center justify-center min-h-screen">
    @yield('content')
</body>

</html>
