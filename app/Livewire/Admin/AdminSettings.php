<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;
use App\Models\AdminSetting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

#[Layout('components.layouts.admin')]
#[Title('Settings')]
class AdminSettings extends Component
{
    use WithFileUploads;

    public string $activeTab = 'general';

    // ── General ──────────────────────────────────────────────────────────────
    public string $site_name        = '';
    public string $site_tagline     = '';
    public string $site_description = '';
    public string $contact_email    = '';
    public string $contact_phone    = '';
    public string $address          = '';
    public string $timezone         = '';
    public string $currency         = '';

    // ── Appearance ───────────────────────────────────────────────────────────
    public string $logo_url       = '';
    public string $favicon_url    = '';
    public string $primary_color  = '#19722d';
    public string $footer_text    = '';

    // ── Booking ──────────────────────────────────────────────────────────────
    public string $default_slot_duration = '60';
    public string $max_advance_days      = '30';
    public string $cancellation_hours    = '24';
    public string $tax_rate              = '0';
    public bool   $allow_guest_booking   = false;

    // ── Notifications ─────────────────────────────────────────────────────────
    public bool   $notify_new_booking   = true;
    public bool   $notify_cancellation  = true;
    public string $notify_admin_email   = '';
    public bool   $whatsapp_notify      = false;

    // ── Social ───────────────────────────────────────────────────────────────
    public string $social_facebook  = '';
    public string $social_instagram = '';
    public string $social_twitter   = '';
    public string $social_youtube   = '';
    public string $social_whatsapp  = '';

    // ── Security ─────────────────────────────────────────────────────────────
    public bool   $maintenance_mode   = false;
    public bool   $registration_open  = true;
    public string $session_lifetime   = '120';

    // ── Profile ──────────────────────────────────────────────────────────────
    public string $profile_name     = '';
    public string $profile_email    = '';
    public string $current_password = '';
    public string $new_password     = '';
    public string $new_password_confirmation = '';

    // ── Mount ─────────────────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->loadAll();
        $user = Auth::user();
        $this->profile_name  = $user->name ?? '';
        $this->profile_email = $user->email ?? '';
    }

    private function loadAll(): void
    {
        $rows = AdminSetting::all()->keyBy('key');

        $map = [
            'site_name','site_tagline','site_description','contact_email','contact_phone',
            'address','timezone','currency',
            'logo_url','favicon_url','primary_color','footer_text',
            'default_slot_duration','max_advance_days','cancellation_hours','tax_rate',
            'notify_admin_email',
            'social_facebook','social_instagram','social_twitter','social_youtube','social_whatsapp',
            'session_lifetime',
        ];

        foreach ($map as $key) {
            if (isset($rows[$key])) {
                $this->$key = (string) ($rows[$key]->value ?? '');
            }
        }

        $booleans = ['allow_guest_booking','notify_new_booking','notify_cancellation',
                     'whatsapp_notify','maintenance_mode','registration_open'];
        foreach ($booleans as $key) {
            if (isset($rows[$key])) {
                $this->$key = (bool) $rows[$key]->value;
            }
        }
    }

    // ── Save handlers ─────────────────────────────────────────────────────────

    public function saveGeneral(): void
    {
        $this->validate([
            'site_name'     => 'required|string|max:100',
            'contact_email' => 'nullable|email|max:100',
            'contact_phone' => 'nullable|string|max:30',
            'currency'      => 'nullable|string|max:10',
        ]);

        $this->persistGroup([
            'site_name','site_tagline','site_description',
            'contact_email','contact_phone','address','timezone','currency',
        ]);

        session()->flash('success_general', 'General settings saved.');
    }

    public function saveAppearance(): void
    {
        $this->validate([
            'primary_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'logo_url'      => 'nullable|string|max:300',
        ]);

        $this->persistGroup(['logo_url','favicon_url','primary_color','footer_text']);
        session()->flash('success_appearance', 'Appearance settings saved.');
    }

    public function saveBooking(): void
    {
        $this->validate([
            'default_slot_duration' => 'required|integer|min:15|max:480',
            'max_advance_days'      => 'required|integer|min:1|max:365',
            'cancellation_hours'    => 'required|integer|min:0|max:168',
            'tax_rate'              => 'required|numeric|min:0|max:100',
        ]);

        $this->persistGroup(['default_slot_duration','max_advance_days','cancellation_hours','tax_rate']);
        AdminSetting::set('allow_guest_booking', $this->allow_guest_booking ? '1' : '0');
        session()->flash('success_booking', 'Booking settings saved.');
    }

    public function saveNotifications(): void
    {
        $this->validate([
            'notify_admin_email' => 'nullable|email|max:100',
        ]);

        AdminSetting::set('notify_new_booking',  $this->notify_new_booking  ? '1' : '0');
        AdminSetting::set('notify_cancellation', $this->notify_cancellation ? '1' : '0');
        AdminSetting::set('notify_admin_email',  $this->notify_admin_email);
        AdminSetting::set('whatsapp_notify',     $this->whatsapp_notify ? '1' : '0');
        session()->flash('success_notifications', 'Notification settings saved.');
    }

    public function saveSocial(): void
    {
        $this->persistGroup(['social_facebook','social_instagram','social_twitter',
                             'social_youtube','social_whatsapp']);
        session()->flash('success_social', 'Social links saved.');
    }

    public function saveSecurity(): void
    {
        $this->validate([
            'session_lifetime' => 'required|integer|min:15|max:1440',
        ]);

        AdminSetting::set('maintenance_mode',  $this->maintenance_mode  ? '1' : '0');
        AdminSetting::set('registration_open', $this->registration_open ? '1' : '0');
        AdminSetting::set('session_lifetime',  $this->session_lifetime);
        session()->flash('success_security', 'Security settings saved.');
    }

    public function saveProfile(): void
    {
        $this->validate([
            'profile_name'  => 'required|string|max:100',
            'profile_email' => 'required|email|max:100',
        ]);

        $user = Auth::user();
        $user->update(['name' => $this->profile_name, 'email' => $this->profile_email]);
        session()->flash('success_profile', 'Profile updated.');
    }

    public function changePassword(): void
    {
        $this->validate([
            'current_password'            => 'required',
            'new_password'                => ['required', 'confirmed', Password::min(8)],
            'new_password_confirmation'   => 'required',
        ]);

        $user = Auth::user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Current password is incorrect.');
            return;
        }

        $user->update(['password' => Hash::make($this->new_password)]);
        $this->current_password = $this->new_password = $this->new_password_confirmation = '';
        session()->flash('success_password', 'Password changed successfully.');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function persistGroup(array $keys): void
    {
        foreach ($keys as $key) {
            AdminSetting::set($key, $this->$key ?? '');
        }
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.admin.admin-settings', [
            'totalUsers'    => User::count(),
            'totalAdmins'   => User::where('role', 'admin')->count(),
            'totalStaff'    => User::whereIn('role', ['staff','facility_owner'])->count(),
            'allSettings'   => AdminSetting::orderBy('group')->orderBy('id')->get()->groupBy('group'),
        ]);
    }
}
