<?php

namespace App\Services\Admin;

use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscription;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class NewsletterCampaignService
{
    public function templateOptions(): array
    {
        return config('newsletter_campaign_templates', []);
    }

    public function templateUsesProducts(string $key): bool
    {
        return (bool) data_get($this->templateOptions(), $key . '.use_products', false);
    }

    public function saveCampaign(array $data, ?int $campaignId, int $adminId): NewsletterCampaign
    {
        if ($campaignId) {
            $campaign = NewsletterCampaign::findOrFail($campaignId);
            $campaign->update($data);

            return $campaign;
        }

        $data['created_by'] = $adminId;

        return NewsletterCampaign::create($data);
    }

    public function deleteCampaign(int $id): void
    {
        NewsletterCampaign::findOrFail($id)->delete();
    }

    public function sendCampaign(NewsletterCampaign $campaign): int
    {
        $subscribers = NewsletterSubscription::query()
            ->where('status', 'subscribed')
            ->orderBy('id')
            ->get(['email', 'name']);

        if ($subscribers->isEmpty()) {
            return 0;
        }

        $products = Product::query()
            ->whereIn('id', $campaign->featured_product_ids ?? [])
            ->where('status', true)
            ->with('categories')
            ->orderByRaw('FIELD(id, ' . implode(',', array_map('intval', $campaign->featured_product_ids ?? [0])) . ')')
            ->get();

        foreach ($subscribers as $subscriber) {
            $html = $this->buildEmailHtml(
                campaign: $campaign,
                subscriberName: $subscriber->name ?: null,
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

        return $subscribers->count();
    }

    public function previewHtml(array $state, Collection $products): string
    {
        return $this->buildEmailHtml(
            campaign: (object) [
                'subject' => $state['subject'] ?: 'Campaign preview',
                'preheader' => $state['preheader'] ?? '',
                'content' => $state['content'] ?? '',
                'button_text' => $state['button_text'] ?? '',
                'button_url' => $state['button_url'] ?? '',
                'template_key' => $state['template_key'] ?? 'announcement',
            ],
            subscriberName: 'Customer',
            products: $products
        );
    }

    public function previewSummary(string $subject, string $preheader, string $templateKey, array $selectedProductIds): array
    {
        return [
            'subject' => trim($subject) ?: 'Campaign preview',
            'preheader' => trim($preheader),
            'template' => data_get($this->templateOptions(), $templateKey . '.name', 'Template'),
            'products' => count($selectedProductIds),
        ];
    }

    protected function buildEmailHtml(object $campaign, ?string $subscriberName, Collection $products): string
    {
        $storeName = Setting::where('key', 'shop_name')->value('value') ?: config('app.name', 'Store');
        $storeSlogan = Setting::where('key', 'site_title')->value('value') ?: '';
        $storeLogo = Setting::where('key', 'shop_logo')->value('value');
        $brandColor = Setting::where('key', 'branding_color')->value('value') ?: '#111111';
        $buttonText = trim((string) ($campaign->button_text ?? '')) ?: 'Shop now';
        $buttonUrl = trim((string) ($campaign->button_url ?? '')) ?: url('/');

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
                                        <div style="margin-top:2px; font-size:11px; color:#6b7280;">' . e($templateType ?: 'announcement') . ' • Automated campaign</div>
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
                        </div>
                    </div>
                </div>
            </div>
        ';
    }
}
