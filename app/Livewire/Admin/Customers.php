<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\UserUser;

#[Layout('components.layouts.admin')]
#[Title('App User')]
class Customers extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $registrationDateFilter = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'registrationDateFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingRegistrationDateFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->registrationDateFilter = '';
        $this->resetPage();
    }

    public function toggleStatus($userId)
    {
        $user = UserUser::find($userId);
        if ($user) {
            $user->is_active = !$user->is_active;
            $user->save();
            
            session()->flash('message', 'User status updated successfully.');
        }
    }

    public function render()
    {
        $query = UserUser::query();

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('phone_number', 'like', '%' . $this->search . '%');
            });
        }

        // Apply status filter
        if ($this->statusFilter) {
            if ($this->statusFilter === 'active') {
                $query->where('is_active', true);
            } elseif ($this->statusFilter === 'inactive') {
                $query->where('is_active', false);
            } elseif ($this->statusFilter === 'premium') {
                $query->where('points', '>=', 1000); // Assuming premium users have 1000+ points
            }
        }

        // Apply registration date filter
        if ($this->registrationDateFilter) {
            $now = now();
            
            if ($this->registrationDateFilter === 'week') {
                $query->where('last_login', '>=', $now->subWeek());
            } elseif ($this->registrationDateFilter === 'month') {
                $query->where('last_login', '>=', $now->subMonth());
            } elseif ($this->registrationDateFilter === 'year') {
                $query->where('last_login', '>=', $now->subYear());
            }
        }

        $users = $query->orderBy('last_login', 'desc')->paginate($this->perPage);

        // Calculate stats
        $totalUsers = UserUser::count();
        $activeUsers = UserUser::where('is_active', true)->count();
        $newThisWeek = UserUser::where('last_login', '>=', now()->subWeek())->count();
        $premiumUsers = UserUser::where('points', '>=', 1000)->count();

        return view('livewire.admin.customers', [
            'users' => $users,
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'newThisWeek' => $newThisWeek,
            'premiumUsers' => $premiumUsers,
        ]);
    }
}