<?php

namespace App\Livewire\Admin;

use App\Models\FlashSale;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class FlashSales extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    public $flashSaleId = null;
    public $title = '';
    public $subtitle = '';
    public $description = '';
    public $saleType = 'percentage';
    public $saleValue = 10;
    public $startsAt = null;
    public $endsAt = null;
    public $active = true;
    public $selectedProductIds = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    protected function rules(): array
    {
        $valueRule = $this->saleType === 'percentage'
            ? 'required|numeric|min:0.01|max:100'
            : 'required|numeric|min:0.01';

        return [
            'title' => 'required|string|max:150',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'saleType' => 'required|in:percentage,fixed',
            'saleValue' => $valueRule,
            'startsAt' => 'required|date',
            'endsAt' => 'required|date|after:startsAt',
            'active' => 'boolean',
            'selectedProductIds' => 'required|array|min:1',
            'selectedProductIds.*' => 'integer|exists:products,id',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function resetForm(): void
    {
        $this->flashSaleId = null;
        $this->title = '';
        $this->subtitle = '';
        $this->description = '';
        $this->saleType = 'percentage';
        $this->saleValue = 10;
        $this->startsAt = now()->format('Y-m-d\TH:i');
        $this->endsAt = now()->addHours(6)->format('Y-m-d\TH:i');
        $this->active = true;
        $this->selectedProductIds = [];
        $this->resetValidation();
    }

    public function mount(): void
    {
        $this->resetForm();
    }

    public function updatedSaleType($value): void
    {
        if ($value === 'percentage' && (float) $this->saleValue > 100) {
            $this->saleValue = 100;
        }
    }

    public function save(): void
    {
        $this->validate();

        $flashSale = FlashSale::updateOrCreate(
            ['id' => $this->flashSaleId],
            [
                'title' => $this->title,
                'subtitle' => $this->subtitle ?: null,
                'description' => $this->description ?: null,
                'sale_type' => $this->saleType,
                'sale_value' => $this->saleValue,
                'starts_at' => $this->startsAt,
                'ends_at' => $this->endsAt,
                'active' => $this->active,
                'created_by' => auth('admin')->id(),
            ]
        );

        $flashSale->products()->sync($this->selectedProductIds);

        session()->flash('message', $this->flashSaleId ? 'Flash sale updated successfully.' : 'Flash sale created successfully.');
        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $sale = FlashSale::with('products')->findOrFail($id);

        $this->flashSaleId = $sale->id;
        $this->title = $sale->title;
        $this->subtitle = $sale->subtitle ?? '';
        $this->description = $sale->description ?? '';
        $this->saleType = $sale->sale_type;
        $this->saleValue = (float) $sale->sale_value;
        $this->startsAt = $sale->starts_at?->format('Y-m-d\TH:i');
        $this->endsAt = $sale->ends_at?->format('Y-m-d\TH:i');
        $this->active = (bool) $sale->active;
        $this->selectedProductIds = $sale->products->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
    }

    public function delete(int $id): void
    {
        FlashSale::findOrFail($id)->delete();
        session()->flash('message', 'Flash sale deleted successfully.');
        if ($this->flashSaleId === $id) {
            $this->resetForm();
        }
    }

    public function render()
    {
        $sales = FlashSale::query()
            ->withCount('products')
            ->orderByDesc('starts_at')
            ->paginate($this->perPage);

        $products = Product::query()
            ->where('status', true)
            ->when($this->search, function ($query) {
                $search = trim((string) $this->search);
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->limit(24)
            ->get();

        $selectedProducts = Product::query()
            ->whereIn('id', $this->selectedProductIds)
            ->get();

        return view('livewire.admin.flash-sales', [
            'flashSales' => $sales,
            'products' => $products,
            'selectedProducts' => $selectedProducts,
            'totalCount' => FlashSale::count(),
            'activeCount' => FlashSale::activeNow()->count(),
            'scheduledCount' => FlashSale::query()
                ->where('active', true)
                ->where('starts_at', '>', now())
                ->count(),
        ]);
    }
}
