<?php

namespace App\Livewire\Store;

use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class ContactForm extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $subject = '';
    public string $message = '';
    public string $successMessage = '';
    public string $supportEmail = '';
    public string $supportPhone = '';

    public function mount(): void
    {
        $user = Auth::guard('web')->user();

        if ($user) {
            $this->name = (string) ($user->name ?? '');
            $this->email = (string) ($user->email ?? '');
            $this->phone = (string) ($user->phone ?? '');
        }

        $this->supportEmail = (string) Setting::where('key', 'email')->value('value');
        $this->supportPhone = (string) Setting::where('key', 'phone')->value('value');
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:120',
            'email' => 'required|email:rfc,dns|max:255',
            'phone' => 'nullable|string|max:50',
            'subject' => 'required|string|max:190',
            'message' => 'required|string|min:10|max:5000',
        ];
    }

    public function submit(): void
    {
        $this->reset('successMessage');
        $data = $this->validate();
        $user = Auth::guard('web')->user();

        $contact = ContactMessage::create([
            'user_id' => $user?->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'subject' => $data['subject'],
            'message' => $data['message'],
            'status' => 'new',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $recipient = $this->resolveRecipient();
        if ($recipient) {
            $body = "New contact message\n\n".
                "Name: {$contact->name}\n".
                "Email: {$contact->email}\n".
                "Phone: ".($contact->phone ?: 'N/A')."\n".
                "Subject: {$contact->subject}\n\n".
                "Message:\n{$contact->message}";

            try {
                Mail::raw($body, function ($message) use ($recipient, $contact) {
                    $message->to($recipient);
                    $message->subject('New contact message: '.$contact->subject);
                });
            } catch (\Throwable $e) {
                // Keep the saved contact message even if mail delivery fails.
            }
        }

        $this->successMessage = 'Your message has been sent. We will get back to you soon.';
        $this->reset('phone', 'subject', 'message');
    }

    protected function resolveRecipient(): ?string
    {
        $settings = Setting::whereIn('key', ['admin_notify_email_enabled', 'admin_notify_email_address', 'email'])->pluck('value', 'key');

        if (! empty($settings['admin_notify_email_enabled']) && ! empty($settings['admin_notify_email_address'])) {
            return (string) $settings['admin_notify_email_address'];
        }

        if (! empty($settings['email'])) {
            return (string) $settings['email'];
        }

        return null;
    }

    public function render()
    {
        return view('livewire.store.contact-form');
    }
}
