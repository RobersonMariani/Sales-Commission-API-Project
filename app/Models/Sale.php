<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    /** @use HasFactory<\Database\Factories\SaleFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'seller_id',
        'value',
        'commission',
        'sale_date',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'value' => 'decimal:2',
        'commission' => 'decimal:2',
        'sale_date' => 'date',
    ];

    /**
     * @return BelongsTo<Seller, $this>
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }
}
