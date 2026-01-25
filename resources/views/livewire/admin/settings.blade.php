<div class="bg-white p-6 rounded-lg shadow table-container mx-auto">
    <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
        <h3 class="text-xl font-bold text-gray-800 flex items-center">
            <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            Store Configuration
        </h3>
    </div>

    @include('admin.includes.message')

    <div class="space-y-8">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-1">
                <h4 class="text-base font-bold text-gray-800">General Identity</h4>
                <p class="text-xs text-gray-500 mt-1">Configure your store's basic information and branding appearance.</p>
            </div>
            <div class="md:col-span-2 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="shop_name" class="block text-sm font-semibold text-gray-700">Shop Name</label>
                        <input wire:model="settings.shop_name" type="text" id="shop_name" class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" placeholder="e.g. My Awesome Store">
                        <p class="text-xs text-gray-500 mt-1">Displayed in emails and invoices.</p>
                        @error('settings.shop_name') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="site_title" class="block text-sm font-semibold text-gray-700">Site Title</label>
                        <input wire:model="settings.site_title" type="text" id="site_title" class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" placeholder="e.g. Best Deals Online">
                        <p class="text-xs text-gray-500 mt-1">Appears in the browser tab.</p>
                        @error('settings.site_title') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label for="logo" class="block text-sm font-semibold text-gray-700">Store Logo</label>
                    <div class="flex items-center space-x-4 mt-2">
                        @if ($settings['shop_logo'])
                            <div class="p-1 border border-gray-200 rounded">
                                <img src="{{ Storage::url($settings['shop_logo']) }}" alt="Logo" class="h-12 w-auto object-contain">
                            </div>
                        @endif
                        <input wire:model="logo" type="file" id="logo" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Recommended format: PNG or JPEG. Max size 2MB.</p>
                    @error('logo') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="branding_color" class="block text-sm font-semibold text-gray-700">Primary Color</label>
                        <div class="flex items-center mt-1">
                            <input wire:model="settings.branding_color" type="color" id="branding_color" class="h-9 w-12 border border-gray-300 rounded p-0.5 cursor-pointer">
                            <input wire:model="settings.branding_color" type="text" class="ml-2 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2 font-mono uppercase" placeholder="#000000">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Main brand color (Buttons, Links).</p>
                        @error('settings.branding_color') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="secondary_color" class="block text-sm font-semibold text-gray-700">Secondary Color</label>
                        <div class="flex items-center mt-1">
                            <input wire:model="settings.secondary_color" type="color" id="secondary_color" class="h-9 w-12 border border-gray-300 rounded p-0.5 cursor-pointer">
                            <input wire:model="settings.secondary_color" type="text" class="ml-2 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2 font-mono uppercase" placeholder="#6B7280">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Accent color for secondary elements.</p>
                        @error('settings.secondary_color') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        <hr class="border-gray-200">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-1">
                <h4 class="text-base font-bold text-gray-800">SEO & Metadata</h4>
                <p class="text-xs text-gray-500 mt-1">Optimize your store for search engines.</p>
            </div>
            <div class="md:col-span-2 space-y-4">
                <div>
                    <label for="meta_description" class="block text-sm font-semibold text-gray-700">Meta Description</label>
                    <textarea wire:model="settings.meta_description" id="meta_description" class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" rows="3" placeholder="Brief summary of your store..."></textarea>
                    <p class="text-xs text-gray-500 mt-1">Recommended length: 150-160 characters.</p>
                    @error('settings.meta_description') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="meta_keywords" class="block text-sm font-semibold text-gray-700">Meta Keywords</label>
                    <input wire:model="settings.meta_keywords" type="text" id="meta_keywords" class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" placeholder="ecommerce, fashion, electronics">
                    <p class="text-xs text-gray-500 mt-1">Separate keywords with commas.</p>
                    @error('settings.meta_keywords') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <hr class="border-gray-200">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-1">
                <h4 class="text-base font-bold text-gray-800">Payment Gateways</h4>
                <p class="text-xs text-gray-500 mt-1">Configure API keys for payment providers. Leave blank to disable a gateway.</p>
            </div>
            <div class="md:col-span-2 space-y-5">
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center mb-2">
                        <span class="font-bold text-gray-700">Stripe</span>
                    </div>
                    <label for="stripe_api_key" class="block text-xs font-semibold text-gray-500 uppercase">Secret Key</label>
                    <input wire:model="settings.stripe_api_key" type="password" id="stripe_api_key" class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2 font-mono" placeholder="sk_live_...">
                    @error('settings.stripe_api_key') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center mb-2">
                        <span class="font-bold text-gray-700">PayPal</span>
                    </div>
                    <label for="paypal_api_key" class="block text-xs font-semibold text-gray-500 uppercase">Client Secret</label>
                    <input wire:model="settings.paypal_api_key" type="password" id="paypal_api_key" class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2 font-mono" placeholder="Paypal Secret Key">
                    @error('settings.paypal_api_key') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center mb-2">
                        <span class="font-bold text-gray-700">SSLCommerz</span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="sslcommerz_store_id" class="block text-xs font-semibold text-gray-500 uppercase">Store ID</label>
                            <input wire:model="settings.sslcommerz_store_id" type="text" id="sslcommerz_store_id" class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2 font-mono" placeholder="your_store_id">
                            @error('settings.sslcommerz_store_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="sslcommerz_api_key" class="block text-xs font-semibold text-gray-500 uppercase">Store Password</label>
                            <input wire:model="settings.sslcommerz_api_key" type="password" id="sslcommerz_api_key" class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2 font-mono" placeholder="Store Password">
                            @error('settings.sslcommerz_api_key') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="mt-2 flex items-center">
                        <input wire:model="settings.sslcommerz_sandbox" type="checkbox" id="ssl_sandbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                        <label for="ssl_sandbox" class="ml-2 text-sm text-gray-600">Enable Sandbox Mode</label>
                    </div>
                </div>
            </div>
        </div>

        <hr class="border-gray-200">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-1">
                <h4 class="text-base font-bold text-gray-800">Store Operations</h4>
                <p class="text-xs text-gray-500 mt-1">Currency and tax configurations.</p>
            </div>
            <div class="md:col-span-2 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="currency" class="block text-sm font-semibold text-gray-700">Default Currency</label>
                        <select wire:model="settings.currency" id="currency" class="form-input mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2">
                            <option value="USD">USD ($)</option>
                            <option value="BDT">BDT (৳)</option>
                            <option value="EUR">EUR (€)</option>
                            <option value="GBP">GBP (£)</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Base currency for products.</p>
                        @error('settings.currency') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="tax_rate" class="block text-sm font-semibold text-gray-700">Tax Rate (%)</label>
                        <div class="relative mt-1">
                            <input wire:model="settings.tax_rate" type="number" id="tax_rate" class="input-field block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2 pr-8" placeholder="0">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">%</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Applied to checkout total.</p>
                        @error('settings.tax_rate') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        <hr class="border-gray-200">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-1">
                <h4 class="text-base font-bold text-gray-800">Social & Contact</h4>
                <p class="text-xs text-gray-500 mt-1">Customer support info and social media links.</p>
            </div>
            <div class="md:col-span-2 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700">Support Email</label>
                        <input wire:model="settings.email" type="email" id="email" class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" placeholder="support@example.com">
                        @error('settings.email') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-semibold text-gray-700">Support Phone</label>
                        <input wire:model="settings.phone" type="text" id="phone" class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" placeholder="+1 234 567 890">
                        @error('settings.phone') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Social Profiles (Full URLs)</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex rounded-md shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-xs">FB</span>
                            <input wire:model="settings.social_facebook" type="url" class="focus:ring-blue-500 focus:border-blue-500 block w-full rounded-none rounded-r-md sm:text-sm border-gray-300 px-3 py-2" placeholder="https://facebook.com/...">
                        </div>
                        <div class="flex rounded-md shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-xs">TW</span>
                            <input wire:model="settings.social_twitter" type="url" class="focus:ring-blue-500 focus:border-blue-500 block w-full rounded-none rounded-r-md sm:text-sm border-gray-300 px-3 py-2" placeholder="https://twitter.com/...">
                        </div>
                        <div class="flex rounded-md shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-xs">IG</span>
                            <input wire:model="settings.social_instagram" type="url" class="focus:ring-blue-500 focus:border-blue-500 block w-full rounded-none rounded-r-md sm:text-sm border-gray-300 px-3 py-2" placeholder="https://instagram.com/...">
                        </div>
                        <div class="flex rounded-md shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-xs">IN</span>
                            <input wire:model="settings.social_linkedin" type="url" class="focus:ring-blue-500 focus:border-blue-500 block w-full rounded-none rounded-r-md sm:text-sm border-gray-300 px-3 py-2" placeholder="https://linkedin.com/in/...">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 pt-5 border-t border-gray-200 flex justify-end">
        <button 
            wire:click="save" 
            wire:loading.attr="disabled" 
            class="bg-blue-600 text-white px-6 py-2.5 rounded-lg hover:bg-blue-700 text-sm font-bold shadow-md transition flex items-center"
        >
            <span wire:loading.remove class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Save Changes
            </span>
            <span wire:loading class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                Saving...
            </span>
        </button>
    </div>
</div>
