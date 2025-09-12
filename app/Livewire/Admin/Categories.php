<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class Categories extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $perPage = 10;
    public $name = '';
    public $subtitle = '';
    public $slug = '';
    public $parent_id = null;
    public $thumbnail;
    public $show_on_homepage = false;
    public $editingId = null;
    public $existingThumbnail = null;
    public $isSlugManuallyEdited = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'subtitle' => 'nullable|string|max:255',
        'slug' => 'required|string|max:255|unique:categories,slug',
        'parent_id' => 'nullable|exists:categories,id',
        'thumbnail' => 'nullable|image|max:2048', // Max 2MB
        'show_on_homepage' => 'boolean',
    ];

    protected $queryString = ['search' => ['except' => ''], 'perPage' => ['except' => 10]];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedName($value)
    {
        if (!$this->isSlugManuallyEdited) {
            $this->slug = Str::slug($value);
        }
    }

    public function updatedSlug()
    {
        $this->isSlugManuallyEdited = true;
    }

    public function resetForm()
    {
        $this->name = '';
        $this->slug = '';
        $this->subtitle = '';
        $this->parent_id = null;
        $this->thumbnail = null;
        $this->show_on_homepage = false;
        $this->editingId = null;
        $this->existingThumbnail = null;
        $this->isSlugManuallyEdited = false;
        $this->resetValidation();
    }

    public function save()
    {
        if ($this->editingId) {
            $this->rules['slug'] = 'required|string|max:255|unique:categories,slug,' . $this->editingId;
            $this->rules['thumbnail'] = 'nullable|image|max:2048';
        }

        $this->validate();

        $data = [
            'name' => $this->name,
            'subtitle' => $this->subtitle,
            'slug' => $this->slug,
            'parent_id' => $this->parent_id,
            'show_on_homepage' => $this->show_on_homepage,
        ];

        if ($this->thumbnail) {
            $path = $this->thumbnail->store('categories', 'public');
            $data['thumbnail'] = $path;
        }

        $category = Category::updateOrCreate(
            ['id' => $this->editingId],
            $data
        );

        session()->flash('message', $this->editingId ? 'Category updated successfully.' : 'Category created successfully.');
        $this->resetForm();
    }

    public function edit($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $this->editingId = $category->id;
        $this->name = $category->name;
        $this->subtitle = $category->subtitle;
        $this->slug = $category->slug;
        $this->parent_id = $category->parent_id;
        $this->show_on_homepage = $category->show_on_homepage;
        $this->existingThumbnail = $category->thumbnail;
        $this->thumbnail = null;
        $this->isSlugManuallyEdited = true; // Assume slug is manually set when editing
    }

    public function deleteCategory($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        if ($category->thumbnail) {
            Storage::disk('public')->delete($category->thumbnail);
        }
        $category->delete();
        session()->flash('message', 'Category deleted successfully.');
        $this->resetForm();
    }

    public function render()
    {
        $categories = Category::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        $parentCategories = Category::whereNull('parent_id')->get();

        return view('livewire.admin.categories', [
            'categories' => $categories,
            'parentCategories' => $parentCategories,
        ]);
    }
}
