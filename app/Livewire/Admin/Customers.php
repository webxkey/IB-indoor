<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\UserUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

#[Layout('components.layouts.admin')]
#[Title('App User')]
class Customers extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $registrationDateFilter = '';
    public $perPage = 10;
    
    // Form properties
    public $showModal = false;
    public $firstName = '';
    public $lastName = '';
    public $email = '';
    public $phoneNumber = '';
    public $password = '';
    public $isActive = true;
    public $sendWelcomeEmail = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'registrationDateFilter' => ['except' => ''],
    ];

    // Validation rules
    protected function rules()
    {
        return [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|unique:users_user,email',
            'phoneNumber' => 'nullable|string|max:20',
            'password' => 'required|min:8',
            'isActive' => 'boolean',
        ];
    }

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

    public function openAddUserModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeAddUserModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->firstName = '';
        $this->lastName = '';
        $this->email = '';
        $this->phoneNumber = '';
        $this->password = '';
        $this->isActive = true;
        $this->sendWelcomeEmail = false;
        $this->resetErrorBag();
    }

    public function addUser()
    {
        $this->validate();

        $user = UserUser::create([
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'phone_number' => $this->phoneNumber,
            'password' => Hash::make($this->password),
            'is_active' => $this->isActive,
            'last_login' => null,
            'is_superuser' => false,
            'is_staff' => false,
            'profile_picture' => null,
            'address' => null,
            'bio' => null,
            'sports_preferences' => null,
            'teams' => null,
            'availability' => null,
            'is_public_profile' => true,
            'is_show_contact' => true,
            'points' => 0,
            'referral_code' => Str::random(10),
        ]);

        if ($user) {
            // Here you could send a welcome email if the checkbox was selected
            if ($this->sendWelcomeEmail) {
                // Email sending logic would go here
            }
            
            session()->flash('success', 'User added successfully.');
            $this->closeAddUserModal();
        } else {
            session()->flash('error', 'Failed to add user. Please try again.');
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
                $query->where('points', '>=', 1000);
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