<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 *     title="Transaction",
 *     description="Transaction model",
 *     @OA\Xml(
 *         name="Transaction"
 *     )
 * )
 */
class Transaction extends Model
{
    use HasFactory;

    /**
     * @OA\Property(
     *     type="integer",
     *     example=2,
     *     description="User id to receive value."
     * )
     */
    private int $payee;

    /**
     * @OA\Property(
     *     type="float",
     *     example=10.0,
     *     description="Transaction amount."
     * )
     */
    private float $value;

    protected $fillable = [
        'payer_id',
        'payee_id',
        'value'
    ];

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    public function payee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payee_id');
    }
}
