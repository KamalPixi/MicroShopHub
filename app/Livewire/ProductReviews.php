<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Review;
use App\Models\Order; // Ensure this Model exists
use Illuminate\Support\Facades\Auth;

class ProductReviews extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $product;
    public $canReview = false; // New property to control visibility
    public $hasPurchased = false; // Specific check for purchase vs status
    
    // Form State
    public $rating = 5;
    public $comment = '';
    public $photos = [];

    protected $rules = [
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'nullable|string|max:1000',
        'photos.*' => 'image|max:2048',
        'photos' => 'max:3',
    ];

    public function mount($product)
    {
        $this->product = $product;
        $this->checkReviewPermission();
    }

    public function checkReviewPermission()
    {
        if (!Auth::check()) {
            $this->canReview = false;
            return;
        }

        // 1. Check if user already reviewed
        $alreadyReviewed = Review::where('user_id', Auth::id())
            ->where('product_id', $this->product->id)
            ->exists();

        if ($alreadyReviewed) {
            $this->canReview = false;
            return;
        }

        // 2. Check if User has a COMPLETED order for this product
        // ADJUSTMENT NEEDED: Check your Order model name and relationship names
        $this->hasPurchased = Order::where('user_id', Auth::id())
            ->where('status', 'completed') // Check your specific status string (e.g. 'delivered', 'completed')
            ->whereHas('items', function ($query) { // Assuming 'items' is the relationship to OrderItem
                $query->where('product_id', $this->product->id);
            })->exists();

        $this->canReview = $this->hasPurchased;
    }

    public function removePhoto($index)
    {
        array_splice($this->photos, $index, 1);
    }

    public function submitReview()
    {
        if (!Auth::check()) return redirect()->route('login'); 

        // Backend Security Check
        $this->checkReviewPermission();
        if (!$this->canReview) {
            $this->dispatch('notify', ['message' => 'You are not eligible to review this product.', 'type' => 'error']);
            return;
        }

        $this->validate();

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
            'media' => $mediaPaths,
        ]);

        $this->reset(['rating', 'comment', 'photos']);
        
        // Re-check permission (to hide form immediately after posting)
        $this->checkReviewPermission();
        
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
