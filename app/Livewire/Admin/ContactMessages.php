<?php

namespace App\Livewire\Admin;

use App\Models\ContactMessage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class ContactMessages extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = 'all';
    public int $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => 'all'],
        'perPage' => ['except' => 10],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function markRead(int $id): void
    {
        $message = ContactMessage::query()->findOrFail($id);
        $message->update([
            'status' => 'read',
            'read_at' => $message->read_at ?? now(),
        ]);

        session()->flash('message', 'Message marked as read.');
    }

    public function markResolved(int $id): void
    {
        $message = ContactMessage::query()->findOrFail($id);
        $message->update([
            'status' => 'resolved',
            'read_at' => $message->read_at ?? now(),
            'resolved_at' => now(),
        ]);

        session()->flash('message', 'Message marked as resolved.');
    }

    public function deleteMessage(int $id): void
    {
        ContactMessage::query()->findOrFail($id)->delete();
        session()->flash('message', 'Message deleted.');
    }

    public function render()
    {
        $messages = [];
        $stats = [
            'total' => 0,
            'new' => 0,
            'read' => 0,
            'resolved' => 0,
        ];

        if (Schema::hasTable('contact_messages')) {
            $query = ContactMessage::query()
                ->when($this->status !== 'all', fn ($q) => $q->where('status', $this->status))
                ->when($this->search, function ($q) {
                    $term = '%' . $this->search . '%';
                    $q->where(function ($sub) use ($term) {
                        $sub->where('name', 'like', $term)
                            ->orWhere('email', 'like', $term)
                            ->orWhere('subject', 'like', $term)
                            ->orWhere('message', 'like', $term);
                    });
                })
                ->orderByDesc('created_at');

            $messages = $query->paginate($this->perPage);

            $stats = [
                'total' => ContactMessage::count(),
                'new' => ContactMessage::where('status', 'new')->count(),
                'read' => ContactMessage::where('status', 'read')->count(),
                'resolved' => ContactMessage::where('status', 'resolved')->count(),
            ];
        } else {
            $messages = new LengthAwarePaginator([], 0, $this->perPage);
        }

        return view('livewire.admin.contact-messages', [
            'messages' => $messages,
            'stats' => $stats,
        ]);
    }
}
