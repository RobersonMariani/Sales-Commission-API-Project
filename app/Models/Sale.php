<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo que representa uma venda no sistema.
 *
 * @mixin IdeHelperSale
 */
class Sale extends Model
{
    /** @use HasFactory<\Database\Factories\SaleFactory> */
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'value',
        'commission',
        'sale_date',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'commission' => 'decimal:2',
        'sale_date' => 'date',
    ];

    /**
     * Retorna o vendedor associado a esta venda.
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }
}
