<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

class ResetUserPassword implements ResetsUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and reset the user's forgotten password.
     *
     * @param  array<string, string>  $input
     */
    public function reset(User $user, array $input): void
    {
        Validator::make($input, [
            'password' => $this->passwordRules(),
        ])->validate();

        $user->forceFill([
            'password' => Hash::make($input['password']),
        ]);

        // If the user is staff, deactivate them so an admin has to re-enable
        if ($user->role === 'staff') {
            $user->forceFill([
                'is_active' => false,
            ]);

            // Deactivate in UserUser table as well
            \App\Models\UserUser::where('email', $user->email)->update(['is_active' => false]);
        }

        $user->save();
    }
}
