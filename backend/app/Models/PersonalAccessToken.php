<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PersonalAccessToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'token',
        'abilities',
        'last_used_at',
        'expires_at',
    ];

    protected $casts = [
        'abilities' => 'json',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a new plaintext token and persist hash
     */
    public static function createTokenForUser(User $user, string $name = 'auth_token', array $abilities = ['*'], ?\DateTimeInterface $expiresAt = null): array
    {
        $plainTextToken = Str::random(40);
        $hashedToken = hash('sha256', $plainTextToken);

        $tokenInstance = self::create([
            'user_id' => $user->id,
            'name' => $name,
            'token' => $hashedToken,
            'abilities' => $abilities,
            'expires_at' => $expiresAt ?? now()->addDays(30),
        ]);

        return [
            'plain_text_token' => $tokenInstance->id . '|' . $plainTextToken,
            'token_instance' => $tokenInstance,
        ];
    }

    /**
     * Find token by bearer string formatted as "<id>|<plainText>" or just "<plainText>"
     */
    public static function findToken(string $tokenString): ?self
    {
        if (str_contains($tokenString, '|')) {
            [$id, $token] = explode('|', $tokenString, 2);
            $instance = self::where('id', $id)->first();
            if ($instance && hash_equals($instance->token, hash('sha256', $token))) {
                if ($instance->expires_at && $instance->expires_at->isPast()) {
                    return null;
                }
                $instance->forceFill(['last_used_at' => now()])->save();
                return $instance;
            }
            return null;
        }

        $hashed = hash('sha256', $tokenString);
        $instance = self::where('token', $hashed)->first();
        if ($instance) {
            if ($instance->expires_at && $instance->expires_at->isPast()) {
                return null;
            }
            $instance->forceFill(['last_used_at' => now()])->save();
            return $instance;
        }

        return null;
    }
}

