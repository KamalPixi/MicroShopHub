<?php

namespace App\Livewire\Admin;

use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscription;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class NewsletterCampaigns extends Component
{
    use WithPagination;

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

    public function mount(): void
    {
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

        if ($this->campaignId) {
            NewsletterCampaign::findOrFail($this->campaignId)->update($data);
            session()->flash('message', 'Campaign updated.');
        } else {
            $data['created_by'] = auth('admin')->id();
            NewsletterCampaign::create($data);
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
        NewsletterCampaign::findOrFail($id)->delete();
        session()->flash('message', 'Campaign deleted.');
    }

    public function sendNow(int $id): void
    {
        $campaign = NewsletterCampaign::findOrFail($id);

        if ($campaign->status === 'sent' && $campaign->sent_at) {
            session()->flash('message', 'This campaign was already sent.');
            return;
        }

        $subscribers = NewsletterSubscription::query()
            ->where('status', 'subscribed')
            ->orderBy('id')
            ->get(['email', 'name']);

        if ($subscribers->isEmpty()) {
            session()->flash('message', 'No active subscribers found.');
            return;
        }

        $products = Product::query()
            ->whereIn('id', $campaign->featured_product_ids ?? [])
            ->where('status', true)
            ->with('categories')
            ->orderByRaw('FIELD(id, ' . implode(',', array_map('intval', $campaign->featured_product_ids ?? [0])) . ')')
            ->get();

        $storeName = Setting::where('key', 'shop_name')->value('value') ?: config('app.name', 'Store');
        $storeSlogan = Setting::where('key', 'site_title')->value('value') ?: '';
        $storeLogo = Setting::where('key', 'shop_logo')->value('value');
        $brandColor = Setting::where('key', 'branding_color')->value('value') ?: '#111111';
        $buttonUrl = $campaign->button_url ?: url('/');
        $buttonText = $campaign->button_text ?: 'Shop now';

        foreach ($subscribers as $subscriber) {
            $html = $this->buildEmailHtml(
                campaign: $campaign,
                subscriberName: $subscriber->name ?: null,
                storeName: $storeName,
                storeSlogan: $storeSlogan,
                storeLogo: $storeLogo,
                brandColor: $brandColor,
                buttonText: $buttonText,
                buttonUrl: $buttonUrl,
                products: $products
            );

            Mail::html($html, function ($message) use ($subscriber, $campaign) {
                $message->to($subscriber->email);
                $message->subject(trim((string) $campaign->subject) ?: 'Newsletter Update');
            });
        }

        $campaign->forceFill([
            'status' => 'sent',
            'sent_at' => now(),
        ])->save();

        session()->flash('message', 'Campaign sent to ' . $subscribers->count() . ' subscribers.');
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
        return config('newsletter_campaign_templates', []);
    }

    public function templateUsesProducts(string $key): bool
    {
        return (bool) data_get($this->templateOptions(), $key . '.use_products', false);
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
        return $this->buildEmailHtml(
            campaign: (object) [
                'subject' => $this->subject ?: 'Campaign preview',
                'preheader' => $this->preheader,
                'content' => $this->content,
                'button_text' => $this->button_text,
                'button_url' => $this->button_url,
                'template_key' => $this->template_key,
            ],
            subscriberName: 'Customer',
            storeName: Setting::where('key', 'shop_name')->value('value') ?: config('app.name', 'Store'),
            storeSlogan: Setting::where('key', 'site_title')->value('value') ?: '',
            storeLogo: Setting::where('key', 'shop_logo')->value('value'),
            brandColor: Setting::where('key', 'branding_color')->value('value') ?: '#111111',
            buttonText: $this->button_text ?: 'Shop now',
            buttonUrl: $this->button_url ?: url('/'),
            products: $products
        );
    }

    public function previewSummary(): array
    {
        return [
            'subject' => trim($this->subject) ?: 'Campaign preview',
            'preheader' => trim($this->preheader),
            'template' => data_get($this->templateOptions(), $this->template_key . '.name', 'Template'),
            'products' => count($this->selected_product_ids),
        ];
    }

    private function buildEmailHtml(
        object $campaign,
        ?string $subscriberName,
        string $storeName,
        string $storeSlogan,
        ?string $storeLogo,
        string $brandColor,
        string $buttonText,
        string $buttonUrl,
        Collection $products
    ): string {
        $subject = e(trim((string) ($campaign->subject ?? '')) ?: 'Newsletter Update');
        $preheader = e($campaign->preheader ?? '');
        $greeting = $subscriberName ? 'Hi ' . e($subscriberName) . ',' : 'Hi there,';
        $content = nl2br(e(trim((string) ($campaign->content ?? ''))));
        $logoHtml = $storeLogo
            ? '<img src="' . e(asset('storage/' . $storeLogo)) . '" alt="' . e($storeName) . '" style="height:42px; width:auto; display:block; object-fit:contain;">'
            : '<div style="font-size:22px;font-weight:800;line-height:1;color:#111;">' . e($storeName) . '</div>';

        $heroColor = e($brandColor ?: '#111111');
        $productCards = '';

        foreach ($products as $product) {
            $image = $product->thumbnail ? asset('storage/' . $product->thumbnail) : null;
            $price = $product->currency_symbol . number_format((float) $product->price, 2);
            $categories = $product->categories->pluck('name')->take(2)->implode(', ');
            $productCards .= '
                <div style="width:260px; border:1px solid #e5e7eb; border-radius:20px; overflow:hidden; background:#fff; display:inline-block; vertical-align:top; margin:0 12px 12px 0; box-shadow:0 10px 30px rgba(15,23,42,0.04);">
                    <div style="background:#f3f4f6; height:158px; overflow:hidden;">
                        ' . ($image ? '<img src="' . e($image) . '" alt="' . e($product->name) . '" style="width:100%; height:160px; object-fit:cover; display:block;">' : '<span style="color:#9ca3af; font-size:12px;">No image</span>') . '
                    </div>
                    <div style="padding:14px;">
                        <div style="font-size:14px; font-weight:800; color:#111827; line-height:1.35; min-height:38px;">' . e(Str::limit($product->name, 70)) . '</div>
                        <div style="margin-top:6px; font-size:12px; color:#6b7280; line-height:1.4;">' . e($categories ?: 'Featured product') . '</div>
                        <div style="margin-top:10px; display:flex; align-items:center; justify-content:space-between; gap:10px;">
                            <div style="font-size:16px; font-weight:800; color:#111827;">' . e($price) . '</div>
                            <a href="' . e(route('store.product.show', $product->slug)) . '" style="display:inline-block; padding:9px 13px; border-radius:999px; background:' . $heroColor . '; color:#fff; font-size:12px; font-weight:700; text-decoration:none;">View</a>
                        </div>
                    </div>
                </div>';
        }

        $templateType = $campaign->template_key ?: 'announcement';

        $templateBody = match ($templateType) {
            'product_showcase' => '
                <div style="margin-top:18px;">
                    <div style="font-size:18px; font-weight:800; color:#111827; margin-bottom:10px;">Featured products</div>
                    <div style="white-space:nowrap; overflow-x:auto; padding-bottom:4px;">' . $productCards . '</div>
                </div>
            ',
            'sale_promo' => '
                <div style="margin-top:18px; padding:18px; border-radius:18px; background:#f8fafc; border:1px solid #e5e7eb;">
                    <div style="font-size:18px; font-weight:800; color:#111827;">' . e($campaign->subject ?: 'New promotion') . '</div>
                    <div style="margin-top:8px; font-size:14px; line-height:1.7; color:#4b5563;">' . $content . '</div>
                    <div style="margin-top:14px;">
                        <a href="' . e($buttonUrl) . '" style="display:inline-block; padding:12px 18px; border-radius:999px; background:' . $heroColor . '; color:#fff; font-size:13px; font-weight:700; text-decoration:none;">' . e($buttonText) . '</a>
                    </div>
                </div>
                ' . (! $products->isEmpty() ? '
                <div style="margin-top:18px;">
                    <div style="font-size:18px; font-weight:800; color:#111827; margin-bottom:10px;">Selected products</div>
                    <div style="white-space:nowrap; overflow-x:auto; padding-bottom:4px;">' . $productCards . '</div>
                </div>' : ''),
            default => ''
        };

        return '
            <div style="background:#f3f4f6; padding:24px;">
                <div style="max-width:740px; margin:0 auto;">
                    <div style="margin-bottom:12px; border-radius:18px; border:1px solid #e5e7eb; background:#fff; padding:12px 16px; box-shadow:0 10px 30px rgba(15,23,42,0.05);">
                        <div style="display:flex; align-items:center; justify-content:space-between; gap:16px;">
                            <div>
                                <div style="font-size:11px; letter-spacing:0.22em; text-transform:uppercase; color:#6b7280;">Live Email Preview</div>
                                <div style="margin-top:4px; font-size:18px; font-weight:800; color:#111827;">' . $subject . '</div>
                                ' . ($preheader ? '<div style="margin-top:4px; font-size:13px; color:#6b7280;">' . $preheader . '</div>' : '') . '
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size:12px; font-weight:700; color:#111827;">' . e($storeName) . '</div>
                                <div style="margin-top:4px; font-size:11px; color:#6b7280;">' . e($storeSlogan ?: 'Store update') . '</div>
                            </div>
                        </div>
                    </div>

                    <div style="background:#fff; border-radius:28px; overflow:hidden; border:1px solid #e5e7eb; box-shadow:0 16px 50px rgba(15,23,42,0.08);">
                        <div style="padding:18px 24px; border-bottom:1px solid #e5e7eb; background:#fafafa;">
                            <div style="display:flex; align-items:center; justify-content:space-between; gap:16px;">
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <div style="width:44px; height:44px; border-radius:14px; overflow:hidden; background:#fff; border:1px solid #e5e7eb; display:flex; align-items:center; justify-content:center;">' . $logoHtml . '</div>
                                    <div>
                                        <div style="font-size:12px; font-weight:700; color:#111827;">' . e($storeName) . '</div>
                                        <div style="margin-top:2px; font-size:11px; color:#6b7280;">' . e($campaign->template_key ?: 'announcement') . ' • Automated campaign</div>
                                    </div>
                                </div>
                                <div style="text-align:right; font-size:11px; color:#6b7280;">
                                    Inbox preview
                                </div>
                            </div>
                        </div>

                        <div style="padding:26px 24px; background:#fff;">
                            <div style="font-size:12px; letter-spacing:0.18em; text-transform:uppercase; color:#6b7280;">' . $subject . '</div>
                            <div style="margin-top:12px; font-size:26px; font-weight:900; line-height:1.15; color:#111827;">' . $subject . '</div>
                            ' . ($preheader ? '<div style="margin-top:8px; font-size:14px; color:#6b7280; line-height:1.6;">' . $preheader . '</div>' : '') . '
                            <div style="margin-top:18px; font-size:15px; line-height:1.8; color:#111827;">' . $greeting . '</div>
                            <div style="margin-top:14px; font-size:15px; line-height:1.8; color:#4b5563;">' . $content . '</div>
                            ' . ($buttonUrl ? '<div style="margin-top:22px;"><a href="' . e($buttonUrl) . '" style="display:inline-block; padding:13px 20px; border-radius:999px; background:' . $heroColor . '; color:#fff; text-decoration:none; font-size:13px; font-weight:800;">' . e($buttonText) . '</a></div>' : '') . '
                            ' . $templateBody . '
                        </div>

                        <div style="padding:18px 24px; border-top:1px solid #e5e7eb; background:#fafafa;">
                            <div style="font-size:12px; color:#6b7280; line-height:1.7;">You are receiving this email because you subscribed to updates from ' . e($storeName) . '.</div>
                            <div style="margin-top:6px; font-size:12px; color:#6b7280;">Powered by your storefront campaign builder.</div>
                        </div>
                    </div>
                </div>
            </div>
        ';
    }
}
