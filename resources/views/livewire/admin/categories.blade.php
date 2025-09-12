<div class="space-y-6">
    <!-- Form Section -->
    <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18m-7 5h7"></path>
            </svg>
            {{ $editingId ? 'Edit Category' : 'Add Category' }}
        </h3>
        <div class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input 
                    wire:model.live="name" 
                    type="text" 
                    id="name" 
                    class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
                    placeholder="Enter category name"
                >
                @error('name') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="subtitle" class="block text-sm font-medium text-gray-700">Subtitle (optional)</label>
                <input 
                    wire:model="subtitle" 
                    type="text" 
                    id="subtitle" 
                    class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
                    placeholder="Enter category subtitle"
                >
                @error('subtitle') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
                <input 
                    wire:model.live="slug" 
                    type="text" 
                    id="slug" 
                    class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
                    placeholder="Enter slug"
                >
                @error('slug') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="parent_id" class="block text-sm font-medium text-gray-700">Parent Category (optional)</label>
                <select 
                    wire:model="parent_id" 
                    id="parent_id" 
                    class="form-input mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2"
                >
                    <option value="">None</option>
                    @foreach ($parentCategories as $parent)
                        @if ($parent->id !== $editingId)
                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                        @endif
                    @endforeach
                </select>
                @error('parent_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="thumbnail" class="block text-sm font-medium text-gray-700">Thumbnail (optional)</label>
                <p class="text-xs text-gray-500 mb-2">
                    Recommended size: <span class="font-medium">400x225px</span> (16:9 ratio)
                </p>
                <input 
                    wire:model="thumbnail" 
                    type="file" 
                    id="thumbnail" 
                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                >
                @if ($existingThumbnail && !$thumbnail)
                    <div class="mt-2">
                        <img src="{{ Storage::url($existingThumbnail) }}" alt="Current Thumbnail" class="h-20 w-20 object-cover rounded">
                    </div>
                @endif
                @error('thumbnail') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Show on Homepage</label>
                <input 
                    wire:model="show_on_homepage" 
                    type="checkbox" 
                    class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                >
                @error('show_on_homepage') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="flex space-x-2">
                <button 
                    wire:click="save" 
                    wire:loading.attr="disabled" 
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm flex items-center"
                >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ $editingId ? 'Update' : 'Save' }}
                </button>
                @if ($editingId)
                    <button 
                        wire:click="resetForm" 
                        class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400 text-sm flex items-center"
                    >
                        Cancel
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- List Section -->
    <div class="bg-white p-4 rounded-lg shadow table-container">
        <div class="flex justify-between">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18m-7 5h7"></path>
                </svg>
                Categories List
            </h3>
        </div>
        <div class="mb-4">
            <label for="search" class="block text-sm font-medium text-gray-700">Search Categories</label>
            <input 
                wire:model.live="search" 
                type="text" 
                id="search" 
                class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" 
                placeholder="Search by category name"
            >
        </div>

        @if (session()->has('message'))
            <div class="mb-4 p-2 bg-green-100 text-green-700 rounded-md text-sm">
                {{ session('message') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="table-field w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="font-medium text-gray-700 p-2">ID</th>
                        <th class="font-medium text-gray-700 p-2">Name</th>
                        <th class="font-medium text-gray-700 p-2">Slug</th>
                        <th class="font-medium text-gray-700 p-2">Parent</th>
                        <th class="font-medium text-gray-700 p-2">Thumbnail</th>
                        <th class="font-medium text-gray-700 p-2">Show on Homepage</th>
                        <th class="font-medium text-gray-700 p-2 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr class="border-t">
                            <td class="p-2">{{ $category->id }}</td>
                            <td class="p-2">
                                {{ $category->name }}
                                <p class="text-xs text-gray-500 mb-2">{{ $category->subtitle }}</p>
                            </td>
                            <td class="p-2">{{ $category->slug }}</td>
                            <td class="p-2">{{ $category->parent ? $category->parent->name : 'None' }}</td>
                            <td class="p-2">
                                @if ($category->thumbnail)
                                    <img src="{{ Storage::url($category->thumbnail) }}" alt="{{ $category->name }}" class="h-12 w-12 object-cover rounded">
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="p-2">
                                <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-full 
                                    {{ $category->show_on_homepage ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $category->show_on_homepage ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td class="p-2 text-end space-x-1">
                                <!-- Edit -->
                                <button 
                                    wire:click="edit({{ $category->id }})" 
                                    class="inline-flex items-center py-1 text-green-600 hover:text-green-800 rounded" 
                                    title="Edit"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" />
                                    </svg>
                                </button>
                                <!-- Delete -->
                                <button 
                                    wire:click="deleteCategory({{ $category->id }})" 
                                    wire:loading.attr="disabled" 
                                    wire:confirm="Are you sure you want to delete this category?" 
                                    class="inline-flex items-center py-1 text-red-600 hover:text-red-800 rounded" 
                                    title="Delete"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3m5 0H6" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-gray-500 py-4">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-container mt-4">
            {{ $categories->links() }}
        </div>
    </div>
</div>
