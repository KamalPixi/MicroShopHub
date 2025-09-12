<div class="bg-white p-4 rounded-lg shadow table-container mx-auto">
    <div class="flex justify-between">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            Shop Settings
        </h3>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 p-2 bg-green-100 text-green-700 rounded-md text-sm">
            {{ session('message') }}
        </div>
    @endif

    <div class="space-y-6">
        <!-- General Settings -->
        <div>
            <h4 class="text-sm font-medium text-gray-700 mb-3">General Settings</h4>
            <div class="space-y-4">
                <div>
                    <label for="shop_name" class="block text-sm font-medium text-gray-700">Shop Name</label>
                    <input 
                        wire:model="settings.shop_name" 
                        type="text" 
                        id="shop_name" 
                        class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
                        placeholder="Enter shop name"
                    >
                    @error('settings.shop_name') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="logo" class="block text-sm font-medium text-gray-700">Shop Logo</label>
                    <input 
                        wire:model="logo" 
                        type="file" 
                        id="logo" 
                        accept="image/*" 
                        class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2"
                    >
                    @error('logo') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    @if ($settings['shop_logo'])
                        <div class="mt-2">
                            <img src="{{ Storage::url($settings['shop_logo']) }}" alt="Shop Logo" class="h-20 w-auto">
                        </div>
                    @endif
                </div>
                <div>
                    <label for="branding_color" class="block text-sm font-medium text-gray-700">Branding Color (Hex)</label>
                    <input 
                        wire:model="settings.branding_color" 
                        type="color" 
                        id="branding_color" 
                        class="input-field mt-1 block w-24 border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2"
                    >
                    @error('settings.branding_color') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="site_title" class="block text-sm font-medium text-gray-700">Site Title</label>
                    <input 
                        wire:model="settings.site_title" 
                        type="text" 
                        id="site_title" 
                        class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
                        placeholder="Enter site title"
                    >
                    @error('settings.site_title') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- SEO Settings -->
        <div>
            <h4 class="text-sm font-medium text-gray-700 mb-3">SEO Settings</h4>
            <div class="space-y-4">
                <div>
                    <label for="meta_description" class="block text-sm font-medium text-gray-700">Meta Description</label>
                    <textarea 
                        wire:model="settings.meta_description" 
                        id="meta_description" 
                        class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
                        placeholder="Enter meta description" 
                        rows="4"
                    ></textarea>
                    @error('settings.meta_description') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="meta_keywords" class="block text-sm font-medium text-gray-700">Meta Keywords</label>
                    <input 
                        wire:model="settings.meta_keywords" 
                        type="text" 
                        id="meta_keywords" 
                        class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
                        placeholder="Enter meta keywords (comma-separated)"
                    >
                    @error('settings.meta_keywords') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Social Media Links -->
        <div>
            <h4 class="text-sm font-medium text-gray-700 mb-3">Social Media Links</h4>
            <div class="space-y-4">
                <div>
                    <label for="social_facebook" class="block text-sm font-medium text-gray-700">Facebook URL</label>
                    <input 
                        wire:model="settings.social_facebook" 
                        type="url" 
                        id="social_facebook" 
                        class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
                        placeholder="Enter Facebook URL"
                    >
                    @error('settings.social_facebook') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="social_twitter" class="block text-sm font-medium text-gray-700">Twitter URL</label>
                    <input 
                        wire:model="settings.social_twitter" 
                        type="url" 
                        id="social_twitter" 
                        class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
                        placeholder="Enter Twitter URL"
                    >
                    @error('settings.social_twitter') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="social_instagram" class="block text-sm font-medium text-gray-700">Instagram URL</label>
                    <input 
                        wire:model="settings.social_instagram" 
                        type="url" 
                        id="social_instagram" 
                        class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
                        placeholder="Enter Instagram URL"
                    >
                    @error('settings.social_instagram') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="social_linkedin" class="block text-sm font-medium text-gray-700">LinkedIn URL</label>
                    <input 
                        wire:model="settings.social_linkedin" 
                        type="url" 
                        id="social_linkedin" 
                        class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
                        placeholder="Enter LinkedIn URL"
                    >
                    @error('settings.social_linkedin') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Payment Gateway Settings -->
        <div>
            <h4 class="text-sm font-medium text-gray-700 mb-3">Payment Gateway API Keys</h4>
            <div class="space-y-4">
                <div>
                    <label for="stripe_api_key" class="block text-sm font-medium text-gray-700">Stripe API Key</label>
                    <input 
                        wire:model="settings.stripe_api_key" 
                        type="password" 
                        id="stripe_api_key" 
                        class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
                        placeholder="Enter Stripe API key"
                    >
                    @error('settings.stripe_api_key') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="paypal_api_key" class="block text-sm font-medium text-gray-700">PayPal API Key</label>
                    <input 
                        wire:model="settings.paypal_api_key" 
                        type="password" 
                        id="paypal_api_key" 
                        class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
                        placeholder="Enter PayPal API key"
                    >
                    @error('settings.paypal_api_key') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="sslcommerz_api_key" class="block text-sm font-medium text-gray-700">SSLCommerz API Key</label>
                    <input 
                        wire:model="settings.sslcommerz_api_key" 
                        type="password" 
                        id="sslcommerz_api_key" 
                        class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
                        placeholder="Enter SSLCommerz API key"
                    >
                    @error('settings.sslcommerz_api_key') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- E-commerce Settings -->
        <div>
            <h4 class="text-sm font-medium text-gray-700 mb-3">E-commerce Settings</h4>
            <div class="space-y-4">
                <div>
                    <label for="currency" class="block text-sm font-medium text-gray-700">Currency</label>
                    <select 
                        wire:model="settings.currency" 
                        id="currency" 
                        class="form-input mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2"
                    >
                        <option value="USD">USD</option>
                        <option value="BDT">BDT</option>
                        <option value="EUR">EUR</option>
                        <option value="GBP">GBP</option>
                    </select>
                    @error('settings.currency') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="tax_rate" class="block text-sm font-medium text-gray-700">Tax Rate (%)</label>
                    <input 
                        wire:model="settings.tax_rate" 
                        type="number" 
                        id="tax_rate" 
                        class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
                        placeholder="Enter tax rate"
                    >
                    @error('settings.tax_rate') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Contact Info -->
        <div>
            <h4 class="text-sm font-medium text-gray-700 mb-3">Contact Info</h4>
            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input 
                        wire:model="settings.email" 
                        type="email" 
                        id="email" 
                        class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
                        placeholder="Enter support email"
                    >
                    @error('settings.email') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                    <input 
                        wire:model="settings.phone" 
                        type="text" 
                        id="phone" 
                        class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
                        placeholder="Enter support phone"
                    >
                    @error('settings.phone') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6">
        <button 
            wire:click="save" 
            wire:loading.attr="disabled" 
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm flex items-center"
        >
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Save Settings
        </button>
    </div>
</div>
