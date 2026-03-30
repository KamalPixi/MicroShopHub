<?php

namespace App\Livewire\Admin;

use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscription;
use App\Models\Product;
use App\Services\Admin\NewsletterCampaignService;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class NewsletterCampaigns extends Component
{
    use WithPagination;

    protected NewsletterCampaignService $service;

    public $campaignId;
    public string $name = '';
    public string $subject = '';
    public string $preheader = '';
    public string $template_key = 'announcement';
    public string $content = '';
    public string $button_text = 'Shop now';
    public string $button_url = '';
    public array $selected_product_ids = [];
    public string $productSearch = '';
    public string $status = 'draft';
    public $scheduled_at;
    public bool $previewCollapsed = false;
    public int $perPage = 10;

    protected $queryString = [
        'perPage' => ['except' => 10],
    ];

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'subject' => ['required', 'string', 'max:200'],
            'preheader' => ['nullable', 'string', 'max:200'],
            'template_key' => ['required', 'string', 'in:' . implode(',', array_keys($this->templateOptions()))],
            'content' => ['nullable', 'string'],
            'button_text' => ['nullable', 'string', 'max:80'],
            'button_url' => ['nullable', 'url', 'max:255'],
            'selected_product_ids' => ['array'],
            'selected_product_ids.*' => ['integer', 'exists:products,id'],
            'status' => ['required', 'string', 'in:draft,scheduled,sent'],
            'scheduled_at' => ['nullable', 'date'],
        ];
    }

    public function mount(NewsletterCampaignService $service): void
    {
        $this->service = $service;
        $this->template_key = array_key_first($this->templateOptions()) ?: 'announcement';
        $this->button_text = 'Shop now';
    }

    public function updatingProductSearch(): void
    {
        //
    }

    public function updatedTemplateKey(): void
    {
        if (! $this->templateUsesProducts($this->template_key)) {
            $this->selected_product_ids = [];
        }
    }

    public function togglePreview(): void
    {
        $this->previewCollapsed = ! $this->previewCollapsed;
    }

    public function resetForm(): void
    {
        $this->campaignId = null;
        $this->name = '';
        $this->subject = '';
        $this->preheader = '';
        $this->template_key = array_key_first($this->templateOptions()) ?: 'announcement';
        $this->content = '';
        $this->button_text = 'Shop now';
        $this->button_url = '';
        $this->selected_product_ids = [];
        $this->productSearch = '';
        $this->status = 'draft';
        $this->scheduled_at = null;
        $this->resetValidation();
    }

    public function save(): void
    {
        $data = $this->validate();

        if (! $this->templateUsesProducts($data['template_key'])) {
            $data['selected_product_ids'] = [];
        }

        $data['button_text'] = $this->button_text ?: null;
        $data['button_url'] = $this->button_url ?: null;
        $data['preheader'] = $this->preheader ?: null;

        if ($data['status'] !== 'scheduled') {
            $data['scheduled_at'] = null;
        }

        $this->service->saveCampaign($data, $this->campaignId, (int) auth('admin')->id());

        if ($this->campaignId) {
            session()->flash('message', 'Campaign updated.');
        } else {
            session()->flash('message', 'Campaign created.');
        }

        $this->resetForm();
    }

    public function edit($id): void
    {
        $campaign = NewsletterCampaign::findOrFail($id);

        $this->campaignId = $campaign->id;
        $this->name = $campaign->name;
        $this->subject = $campaign->subject;
        $this->preheader = $campaign->preheader ?? '';
        $this->template_key = $campaign->template_key ?: 'announcement';
        $this->content = $campaign->content ?? '';
        $this->button_text = $campaign->button_text ?: 'Shop now';
        $this->button_url = $campaign->button_url ?? '';
        $this->selected_product_ids = $campaign->featured_product_ids ?? [];
        $this->status = $campaign->status;
        $this->scheduled_at = $campaign->scheduled_at?->format('Y-m-d\TH:i');
    }

    public function delete($id): void
    {
        $this->service->deleteCampaign((int) $id);
        session()->flash('message', 'Campaign deleted.');
    }

    public function sendNow(int $id): void
    {
        $campaign = NewsletterCampaign::findOrFail($id);

        if ($campaign->status === 'sent' && $campaign->sent_at) {
            session()->flash('message', 'This campaign was already sent.');
            return;
        }

        $sentCount = $this->service->sendCampaign($campaign);

        if ($sentCount === 0) {
            session()->flash('message', 'No active subscribers found.');
            return;
        }

        session()->flash('message', 'Campaign sent to ' . $sentCount . ' subscribers.');
    }

    public function render()
    {
        $campaigns = NewsletterCampaign::query()
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        $products = Product::query()
            ->where('status', true)
            ->when($this->productSearch, function ($query) {
                $query->where('name', 'like', '%' . $this->productSearch . '%')
                    ->orWhere('sku', 'like', '%' . $this->productSearch . '%');
            })
            ->orderBy('created_at', 'desc')
            ->limit(24)
            ->get();

        $selectedProducts = Product::query()
            ->whereIn('id', $this->selected_product_ids ?: [0])
            ->where('status', true)
            ->get()
            ->sortBy(fn ($product) => array_search($product->id, $this->selected_product_ids))
            ->values();

        return view('livewire.admin.newsletter-campaigns', [
            'campaigns' => $campaigns,
            'totalCount' => NewsletterCampaign::count(),
            'scheduledCount' => NewsletterCampaign::where('status', 'scheduled')->count(),
            'sentCount' => NewsletterCampaign::where('status', 'sent')->count(),
            'subscriberCount' => NewsletterSubscription::where('status', 'subscribed')->count(),
            'products' => $products,
            'selectedProducts' => $selectedProducts,
            'templateOptions' => $this->templateOptions(),
            'previewHtml' => $this->buildPreviewHtml($selectedProducts),
            'previewSummary' => $this->previewSummary(),
        ]);
    }

    public function templateOptions(): array
    {
        return $this->service->templateOptions();
    }

    public function templateUsesProducts(string $key): bool
    {
        return $this->service->templateUsesProducts($key);
    }

    public function toggleProduct(int $productId): void
    {
        if (in_array($productId, $this->selected_product_ids, true)) {
            $this->selected_product_ids = array_values(array_filter(
                $this->selected_product_ids,
                fn ($id) => (int) $id !== $productId
            ));
            return;
        }

        $this->selected_product_ids[] = $productId;
        $this->selected_product_ids = array_values(array_unique(array_map('intval', $this->selected_product_ids)));
    }

    private function buildPreviewHtml(Collection $products): string
    {
        return $this->service->previewHtml([
            'subject' => $this->subject,
            'preheader' => $this->preheader,
            'content' => $this->content,
            'button_text' => $this->button_text,
            'button_url' => $this->button_url,
            'template_key' => $this->template_key,
        ], $products);
    }

    public function previewSummary(): array
    {
        return $this->service->previewSummary(
            $this->subject,
            $this->preheader,
            $this->template_key,
            $this->selected_product_ids
        );
    }
}
