<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Order;
use Carbon\Carbon;
use Livewire\Attributes\Computed;

class OrderList extends Component
{
    use WithPagination;

    #[Url]
    public $statusFilter = '';

    #[Url]
    public $fromDate = '';

    #[Url]
    public $toDate = '';

    #[Url]
    public $search = '';

    public $perPage = 7;
    public $stats = [];

    public $allowedStatuses = [
        'pending',
        'in_progress',
        'completed',
        'cancelled'
    ];

    public function mount()
    {
        if (empty($this->fromDate)) {
            $this->fromDate = Carbon::now()->subDays(30)->format('Y-m-d');
        }
        if (empty($this->toDate)) {
            $this->toDate = Carbon::now()->format('Y-m-d');
        }

        $this->updateStatusCounts();
    }

    public function updating($name, $value)
    {
        if (in_array($name, ['statusFilter', 'fromDate', 'toDate', 'search'])) {
            $this->resetPage();
        }
    }

    public function updated($property)
    {
        if (in_array($property, ['statusFilter', 'fromDate', 'toDate', 'search'])) {
            $this->resetPage();
            $this->updateStatusCounts();
        }
    }

    public function updateOrderStatus($orderId, $newStatus)
    {
        try {
            if (!in_array($newStatus, $this->allowedStatuses)) {
                throw new \Exception('Invalid status provided');
            }

            $order = Order::findOrFail($orderId);
            $order->status = $newStatus;
            $order->save();

            $this->updateStatusCounts();

            $this->dispatch('showNotification', [
                'type' => 'success',
                'message' => "Order #{$orderId} status updated to " . ucfirst($newStatus)
            ]);
        } catch (\Exception $e) {
            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => "Error updating order status: " . $e->getMessage()
            ]);
        }
    }

    private function getFilteredQuery()
    {
        return Order::query()
            ->when($this->statusFilter, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($this->fromDate, function ($query, $date) {
                return $query->whereDate('created_at', '>=', $date);
            })
            ->when($this->toDate, function ($query, $date) {
                return $query->whereDate('created_at', '<=', $date);
            })
            ->when($this->search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('pickup_location', 'like', '%' . $search . '%')
                        ->orWhere('delivery_location', 'like', '%' . $search . '%')
                        ->orWhere('id', 'like', '%' . $search . '%');
                });
            });
    }

    private function updateStatusCounts()
    {
        $counts = $this->getFilteredQuery()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $this->stats = array_merge([
            'pending' => 0,
            'in_progress' => 0,
            'completed' => 0,
            'cancelled' => 0
        ], $counts);

        $this->dispatch('statusCountsUpdated', $this->stats);
    }

    #[Computed]
    public function orders()
    {
        return $this->getFilteredQuery()
            ->latest()
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.admin.order-list', [
            'orders' => $this->orders,
            'stats' => $this->stats,
            'statusColors' => [
                'pending' => 'bg-yellow-100 text-yellow-800',
                'in_progress' => 'bg-blue-100 text-blue-800',
                'completed' => 'bg-green-100 text-green-800',
                'cancelled' => 'bg-red-100 text-red-800',
            ]
        ]);
    }
}
