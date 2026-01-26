<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-6" id="reviews">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <div class="lg:col-span-4">
            <div class="bg-gray-50 rounded-xl p-4 text-center border border-gray-100 sticky top-24">
                <h3 class="text-xs font-bold text-gray-900 mb-2 uppercase tracking-wide">Customer Reviews</h3>
                
                <div class="flex items-center justify-center items-baseline gap-2 mb-2">
                    <span class="text-3xl font-extrabold text-gray-900">{{ number_format($avgRating, 1) }}</span>
                    <span class="text-xs text-gray-400 font-medium">/ 5.0</span>
                </div>

                <div class="flex justify-center mb-1">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-4 h-4 {{ $i <= round($avgRating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                
                <p class="text-[10px] text-gray-500 font-medium">{{ $totalReviews }} Verified Reviews</p>
            </div>
        </div>

        <div class="lg:col-span-8 space-y-6">
            
            <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                @auth
                    @if($canReview)
                        <form wire:submit.prevent="submitReview">
                            <div class="flex flex-col md:flex-row gap-3 items-start md:items-center">
                                <div class="shrink-0" x-data="{ hoverRating: 0 }">
                                    <div class="flex items-center space-x-1" @mouseleave="hoverRating = 0">
                                        @for($i = 1; $i <= 5; $i++)
                                            <button type="button" 
                                                wire:click="$set('rating', {{ $i }})" 
                                                @mouseover="hoverRating = {{ $i }}"
                                                class="focus:outline-none transition-transform hover:scale-110 p-1">
                                                <svg class="w-6 h-6 transition-colors duration-200" 
                                                     :class="(hoverRating >= {{ $i }}) || (hoverRating === 0 && $wire.rating >= {{ $i }}) ? 'text-yellow-400' : 'text-gray-200'"
                                                     fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            </button>
                                        @endfor
                                    </div>
                                </div>

                                <div class="flex-1 w-full flex gap-2 relative">
                                    <div class="relative flex-1">
                                        <textarea wire:model="comment" rows="1" 
                                            class="w-full border border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary text-xs py-2.5 pl-3 pr-10 resize-none placeholder-gray-400 min-h-[42px]"
                                            placeholder="Write review..."></textarea>
                                        
                                        <div class="absolute right-2 top-1.5">
                                            <label class="cursor-pointer p-1.5 rounded-full hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition flex items-center justify-center">
                                                <input type="file" wire:model="photos" multiple class="hidden" accept="image/*">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            </label>
                                        </div>
                                    </div>

                                    <button type="submit" 
                                        class="bg-gray-900 text-white text-xs font-bold px-4 rounded-lg shadow-sm hover:bg-black transition-all flex items-center justify-center shrink-0 h-[42px]">
                                        <span wire:loading.remove>Post</span>
                                        <span wire:loading><svg class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></span>
                                    </button>
                                </div>
                            </div>
                            
                            @error('comment') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                            @error('rating') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                            @error('photos.*') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror

                            @if ($photos)
                                <div class="flex gap-2 mt-3 overflow-x-auto pb-1">
                                    @foreach ($photos as $index => $photo)
                                        <div class="relative w-12 h-12 rounded-lg overflow-hidden border border-gray-200 shrink-0 group">
                                            <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                                            <button type="button" wire:click="removePhoto({{ $index }})" class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition text-white">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </form>
                    @else
                        <div class="flex items-center justify-between py-1">
                            <div class="flex items-center text-gray-500">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="text-xs">
                                    @if($hasPurchased)
                                        You have already reviewed this product.
                                    @else
                                        Please make a purchase to share your feedback.
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="flex items-center justify-between py-1">
                        <p class="text-gray-500 text-xs">Login to share your experience.</p>
                        <a href="{{ route('login') }}" class="inline-block bg-white border border-gray-300 text-gray-700 text-xs font-bold px-4 py-1.5 rounded-lg shadow-sm hover:bg-gray-50 transition">Log In</a>
                    </div>
                @endauth
            </div>

            <div>
                <h4 class="font-bold text-gray-900 mb-3 text-xs uppercase tracking-wide border-b border-gray-100 pb-2">Latest Reviews</h4>

                <div class="space-y-3">
                    @forelse($reviews as $review)
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 transition hover:shadow-sm">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 font-bold text-[10px] shadow-sm">
                                        {{ substr($review->user->name ?? 'U', 0, 1) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <h5 class="font-bold text-gray-900 text-xs">{{ $review->user->name }}</h5>
                                        <span class="text-[10px] text-gray-400 block">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <div class="flex">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-3 h-3 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                            
                            <p class="text-gray-600 text-xs leading-relaxed pl-8">
                                {{ $review->comment }}
                            </p>

                            @if(!empty($review->media))
                                <div class="flex gap-2 mt-3 pl-8">
                                    @foreach($review->media as $mediaPath)
                                        <a href="{{ Storage::url($mediaPath) }}" target="_blank" class="w-12 h-12 rounded-lg border border-gray-200 overflow-hidden block hover:opacity-80 transition">
                                            <img src="{{ Storage::url($mediaPath) }}" class="w-full h-full object-cover">
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-6 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                            <p class="text-gray-400 text-xs font-medium">No reviews yet.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-4">
                    {{ $reviews->links() }}
                </div>
            </div>
        </div>

    </div>
</div>
