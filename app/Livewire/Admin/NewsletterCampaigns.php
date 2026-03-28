<?php

namespace App\Livewire\Admin;

use App\Models\NewsletterSubscription;
use App\Models\NewsletterCampaign;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithPagination;

class NewsletterCampaigns extends Component
{
    use WithPagination;

    public $campaignId;
    public $name = '';
    public $subject = '';
    public $content = '';
    public $status = 'draft';
    public $scheduled_at;
    public $perPage = 10;

    protected $queryString = [
        'perPage' => ['except' => 10],
    ];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:150',
            'subject' => 'required|string|max:200',
            'content' => 'nullable|string',
            'status' => 'required|string|in:draft,scheduled,sent',
            'scheduled_at' => 'nullable|date',
        ];
    }

    public function save()
    {
        $data = $this->validate();

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

    public function edit($id)
    {
        $campaign = NewsletterCampaign::findOrFail($id);

        $this->campaignId = $campaign->id;
        $this->name = $campaign->name;
        $this->subject = $campaign->subject;
        $this->content = $campaign->content ?? '';
        $this->status = $campaign->status;
        $this->scheduled_at = $campaign->scheduled_at?->format('Y-m-d\TH:i');
    }

    public function delete($id)
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

        $subject = trim((string) $campaign->subject);
        $body = trim((string) ($campaign->content ?? ''));
        $footer = "\n\n---\nYou are receiving this email because you subscribed to our newsletter.";

        foreach ($subscribers as $subscriber) {
            $personalizedBody = $body;
            if ($subscriber->name) {
                $personalizedBody = "Hi {$subscriber->name},\n\n" . $personalizedBody;
            }

            Mail::raw($personalizedBody . $footer, function ($message) use ($subscriber, $subject) {
                $message->to($subscriber->email);
                $message->subject($subject ?: 'Newsletter Update');
            });
        }

        $campaign->forceFill([
            'status' => 'sent',
            'sent_at' => now(),
        ])->save();

        session()->flash('message', 'Campaign sent to ' . $subscribers->count() . ' subscribers.');
    }

    public function resetForm()
    {
        $this->campaignId = null;
        $this->name = '';
        $this->subject = '';
        $this->content = '';
        $this->status = 'draft';
        $this->scheduled_at = null;
    }

    public function render()
    {
        $campaigns = NewsletterCampaign::orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.newsletter-campaigns', [
            'campaigns' => $campaigns,
            'totalCount' => NewsletterCampaign::count(),
            'scheduledCount' => NewsletterCampaign::where('status', 'scheduled')->count(),
            'sentCount' => NewsletterCampaign::where('status', 'sent')->count(),
            'subscriberCount' => NewsletterSubscription::where('status', 'subscribed')->count(),
        ]);
    }
}
