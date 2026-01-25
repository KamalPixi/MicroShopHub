<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads; // Import this
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class ProductReviews extends Component
{
    use WithPagination;
    use WithFileUploads; // Use this

    public $product;
    
    // Form State
    public $rating = 5;
    public $comment = '';
    public $photos = []; // New property for temporary uploads

    protected $rules = [
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'nullable|string|max:1000',
        'photos.*' => 'image|max:2048', // Max 2MB per image
        'photos' => 'max:3', // Limit to 3 photos
    ];

    public function mount($product)
    {
        $this->product = $product;
    }

    public function removePhoto($index)
    {
        array_splice($this->photos, $index, 1);
    }

    public function submitReview()
    {
        if (!Auth::check()) {
            return redirect()->route('login'); 
        }

        $this->validate();

        $existing = Review::where('user_id', Auth::id())
            ->where('product_id', $this->product->id)
            ->first();

        if ($existing) {
            $this->dispatch('notify', ['message' => 'You have already reviewed this product.', 'type' => 'error']);
            return;
        }

        // Handle File Uploads
        $mediaPaths = [];
        foreach ($this->photos as $photo) {
            $mediaPaths[] = $photo->store('reviews', 'public');
        }

        Review::create([
            'user_id' => Auth::id(),
            'product_id' => $this->product->id,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'status' => true,
            'media' => $mediaPaths, // Store array of paths
        ]);

        $this->reset(['rating', 'comment', 'photos']);
        $this->dispatch('notify', ['message' => 'Review submitted!', 'type' => 'success']);
    }

    public function render()
    {
        return view('livewire.product-reviews', [
            'reviews' => $this->product->reviews()->with('user')->paginate(5),
            'avgRating' => $this->product->average_rating,
            'totalReviews' => $this->product->review_count
        ]);
    }
}
