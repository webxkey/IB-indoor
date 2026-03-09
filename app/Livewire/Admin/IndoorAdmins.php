<?php

namespace App\Livewire\Admin;

use Exception;
use App\Models\User;
use App\Models\BookingVenue;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

#[Layout('components.layouts.admin')]
#[Title('Indoor Admins')]
class IndoorAdmins extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $venueFilter = '';

    // Create form properties
    public $name;
    public $contactNumber;
    public $email;
    public $password;
    public $confirmPassword;
    public $complexId;

    // Edit form properties
    public $editIndoorAdminId;
    public $editName;
    public $editContactNumber;
    public $editEmail;
    public $editPassword;
    public $editConfirmPassword;
    public $editComplexId;

    // Reset password properties
    public $resetPasswordAdminId;
    public $resetPasswordAdminName;
    public $newPassword;
    public $confirmNewPassword;

    // View details
    public $viewAdmin = null;

    public $deleteId;

    protected $queryString = ['search', 'statusFilter', 'venueFilter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = User::where('role', 'facility_owner')
            ->with('complex');

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('contact', 'like', '%' . $this->search . '%');
            });
        }

        // Apply venue filter
        if ($this->venueFilter) {
            $query->where('complex_id', $this->venueFilter);
        }

        $indoorAdmins = $query->orderBy('created_at', 'desc')->paginate(10);
        $venues = BookingVenue::orderBy('name')->get();

        return view('livewire.admin.indoor-admins', [
            'indoorAdmins' => $indoorAdmins,
            'venues' => $venues,
        ]);
    }

    public function createIndoorAdmin()
    {
        $this->resetCreateForm();
        $this->js("$('#createIndoorAdminModal').modal('show')");
    }

    public function resetCreateForm()
    {
        $this->name = '';
        $this->contactNumber = '';
        $this->email = '';
        $this->password = '';
        $this->confirmPassword = '';
        $this->complexId = '';
        $this->resetErrorBag();
    }

    public function saveIndoorAdmin()
    {
        $this->validate([
            'name' => 'required|min:2|max:255',
            'contactNumber' => 'required|max:20',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'confirmPassword' => 'required|same:password',
            'complexId' => 'required|exists:booking_venue,id',
        ], [
            'complexId.required' => 'Please select a venue/complex.',
            'complexId.exists' => 'The selected venue/complex does not exist.',
            'confirmPassword.same' => 'The passwords do not match.',
        ]);

        try {
            User::create([
                'name' => $this->name,
                'contact' => $this->contactNumber,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => 'facility_owner',
                'complex_id' => $this->complexId,
            ]);

            $this->js("Swal.fire('Success!', 'Indoor Admin Created Successfully', 'success')");
            $this->js('$("#createIndoorAdminModal").modal("hide")');
            $this->resetCreateForm();
        } catch (Exception $e) {
            $this->js("Swal.fire('Error!', '" . addslashes($e->getMessage()) . "', 'error')");
        }
    }

    public function editIndoorAdmin($id)
    {
        $user = User::find($id);
        if (!$user) {
            $this->js("Swal.fire('Error!', 'Indoor Admin Not Found', 'error')");
            return;
        }

        $this->editIndoorAdminId = $user->id;
        $this->editName = $user->name;
        $this->editContactNumber = $user->contact;
        $this->editEmail = $user->email;
        $this->editComplexId = $user->complex_id;
        $this->editPassword = '';
        $this->editConfirmPassword = '';
        $this->resetErrorBag();

        $this->dispatch('edit-indoor-admin-modal');
    }

    public function updateIndoorAdmin()
    {
        $validationRules = [
            'editName' => 'required|min:2|max:255',
            'editContactNumber' => 'required|max:20',
            'editEmail' => 'required|email|unique:users,email,' . $this->editIndoorAdminId,
            'editComplexId' => 'required|exists:booking_venue,id',
        ];

        // Only validate password if provided
        if (!empty($this->editPassword)) {
            $validationRules['editPassword'] = 'required|min:8';
            $validationRules['editConfirmPassword'] = 'required|same:editPassword';
        }

        $this->validate($validationRules, [
            'editComplexId.required' => 'Please select a venue/complex.',
            'editComplexId.exists' => 'The selected venue/complex does not exist.',
            'editConfirmPassword.same' => 'The passwords do not match.',
        ]);

        try {
            $user = User::find($this->editIndoorAdminId);
            if ($user) {
                $user->name = $this->editName;
                $user->contact = $this->editContactNumber;
                $user->email = $this->editEmail;
                $user->complex_id = $this->editComplexId;

                if (!empty($this->editPassword)) {
                    $user->password = Hash::make($this->editPassword);
                }

                $user->save();
                $this->js("Swal.fire('Success!', 'Indoor Admin Updated Successfully', 'success')");
            } else {
                $this->js("Swal.fire('Error!', 'Indoor Admin Not Found', 'error')");
            }
        } catch (Exception $e) {
            $this->js("Swal.fire('Error!', '" . addslashes($e->getMessage()) . "', 'error')");
        }

        $this->js('$("#editIndoorAdminModal").modal("hide")');
    }

    public function viewDetails($id)
    {
        $this->viewAdmin = User::with('complex')->find($id);
        if (!$this->viewAdmin) {
            $this->js("Swal.fire('Error!', 'Indoor Admin Not Found', 'error')");
            return;
        }
        $this->dispatch('view-indoor-admin-modal');
    }

    public function openResetPasswordModal($id)
    {
        $user = User::find($id);
        if (!$user) {
            $this->js("Swal.fire('Error!', 'Indoor Admin Not Found', 'error')");
            return;
        }

        $this->resetPasswordAdminId = $user->id;
        $this->resetPasswordAdminName = $user->name;
        $this->newPassword = '';
        $this->confirmNewPassword = '';
        $this->resetErrorBag();

        $this->dispatch('reset-password-modal');
    }

    public function resetPassword()
    {
        $this->validate([
            'newPassword' => 'required|min:8',
            'confirmNewPassword' => 'required|same:newPassword',
        ], [
            'confirmNewPassword.same' => 'The passwords do not match.',
        ]);

        try {
            $user = User::find($this->resetPasswordAdminId);
            if ($user) {
                $user->password = Hash::make($this->newPassword);
                $user->save();
                $this->js("Swal.fire('Success!', 'Password Reset Successfully', 'success')");
            } else {
                $this->js("Swal.fire('Error!', 'Indoor Admin Not Found', 'error')");
            }
        } catch (Exception $e) {
            $this->js("Swal.fire('Error!', '" . addslashes($e->getMessage()) . "', 'error')");
        }

        $this->js('$("#resetPasswordModal").modal("hide")');
        $this->newPassword = '';
        $this->confirmNewPassword = '';
    }

    public function generatePassword()
    {
        $password = Str::random(12);
        $this->newPassword = $password;
        $this->confirmNewPassword = $password;
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->dispatch('confirm-delete-indoor-admin');
    }

    #[On('confirmDeleteIndoorAdmin')]
    public function deleteIndoorAdmin()
    {
        $user = User::find($this->deleteId);
        if ($user) {
            $user->delete();
            $this->js("Swal.fire('Deleted!', 'Indoor Admin has been deleted.', 'success')");
        } else {
            $this->js("Swal.fire('Error!', 'Indoor Admin Not Found', 'error')");
        }
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->venueFilter = '';
        $this->resetPage();
    }
}
