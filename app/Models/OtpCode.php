<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OtpCode extends Model
{
    use HasFactory;

    protected $table = 'otp_codes';

    protected $fillable = [
        'user_id',
        'email',
        'code',
        'type',
        'is_used',
        'expires_at',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship with User model
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get valid (not expired, not used) OTP codes
     */
    public function scopeValid($query)
    {
        return $query->where('is_used', false)
            ->where('expires_at', '>', now());
    }

    /**
     * Scope to find by email and type
     */
    public function scopeByEmailAndType($query, $email, $type)
    {
        return $query->where('email', $email)->where('type', $type);
    }

    /**
     * Check if OTP is valid (not expired, not used)
     */
    public function isValid(): bool
    {
        return !$this->is_used && $this->expires_at > now();
    }

    /**
     * Check if OTP is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at <= now();
    }

    /**
     * Mark OTP as used
     */
    public function markAsUsed(): void
    {
        $this->update(['is_used' => true]);
    }

    /**
     * Generate random 6-digit OTP code
     */
    public static function generateCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Create OTP code for email
     */
    public static function createForEmail(string $email, string $type = 'email_verification', ?int $userId = null)
    {
        // Invalidate previous codes for this email and type
        static::byEmailAndType($email, $type)->update(['is_used' => true]);

        return static::create([
            'user_id' => $userId,
            'email' => $email,
            'code' => static::generateCode(),
            'type' => $type,
            'expires_at' => now()->addMinutes(15), // 15 minutes validity
        ]);
    }

    /**
     * Verify OTP code
     */
    public static function verify(string $email, string $code, string $type = 'email_verification'): ?self
    {
        $otp = static::byEmailAndType($email, $type)
            ->valid()
            ->where('code', $code)
            ->first();

        return $otp;
    }
}
