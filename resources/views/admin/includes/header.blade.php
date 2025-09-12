<header class="bg-white shadow p-3 mb-2 rounded-lg flex justify-between items-center">
    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
        <!-- Floating Toggle Button (Visible when sidebar is collapsed) -->
        <button id="toggle-sidebar-floating" class="text-gray-900 hover:bg-gray-200 rounded z-10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
        </button>

        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2M9 19"></path>
        </svg>
        Dashboard
    </h2>
    <div class="flex items-center">
        <span class="text-gray-600 text-sm mr-3">Admin</span>
        <a href="{{ route('admin.logout') }}" class="text-blue-600 hover:underline text-sm flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7"></path>
            </svg>
            Signout
        </a>
    </div>
</header>
