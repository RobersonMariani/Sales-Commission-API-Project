<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo que representa um vendedor no sistema.
 *
 * @mixin IdeHelperSeller
 */
class Seller extends Model
{
    /** @use HasFactory<\Database\Factories\SellerFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
    ];

    /**
     * Retorna as vendas associadas a este vendedor.
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
