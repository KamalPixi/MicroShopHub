<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - MicroShopHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/heroicons@2.1.1/dist/heroicons.min.js"></script>
    <style>
        /* Smooth transitions and modern scrollbar */
        button:hover,
        input:focus {
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
    </style>
</head>

<body class="bg-gray-50 font-sans antialiased flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-lg">
        <!-- Logo and Title -->
        <div class="flex items-center justify-center mb-6">
            <svg class="w-8 h-8 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18v18H3V3z"></path>
            </svg>
            <h1 class="text-2xl font-bold text-gray-800">Admin Login</h1>
        </div>

        <!-- Login Form -->
        @livewire('Admin.Login')

        <!-- Forgot Password Link -->
        <div class="mt-4 text-center">
            <a href="{{ route('admin.password.request') }}" class="text-sm text-blue-600 hover:underline">Forgot your password?</a>
        </div>
    </div>
</body>

</html>
