<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @OA\Schema(
 *     title="User",
 *     description="User model",
 *     @OA\Xml(
 *         name="User"
 *     )
 * )
 */
class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * @OA\Property(type="string", example="Marcus")
     */
    private string $name;

    /**
     * @OA\Property(type="string", example="marcus@email.com")
     */
    private string $email;

    /**
     * @OA\Property(type="string", example="123456")
     */
    private string $password;

    /**
     * @OA\Property(
     *     type="string",
     *     enum={"comum", "lojista"},
     *     example="comum"
     * )
     */
    private string $type;

    /**
     * @OA\Property(type="string", example="014.113.145-66")
     */
    private string  $cpf_cnpj;

    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'cpf_cnpj'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Transaction::class, 'payer_id');
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(Transaction::class, 'payee_id');
    }
}
