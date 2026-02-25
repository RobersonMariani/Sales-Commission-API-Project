<?php

declare(strict_types=1);

namespace App\Api\Modules\Sale\Jobs;

use App\Api\Modules\Sale\Mail\DailySellerCommissionMail;
use App\Models\Seller;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendDailySellerCommissionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly Seller $seller,
        public readonly string $date,
    ) {}

    public function handle(): void
    {
        /** @var object{count: int, total_value: float, total_commission: float} $summary */
        $summary = DB::table('sales')
            ->where('seller_id', $this->seller->id)
            ->where('sale_date', $this->date)
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(value), 0) as total_value, COALESCE(SUM(commission), 0) as total_commission')
            ->first();

        if ($summary->count === 0) {
            return;
        }

        Mail::to($this->seller->email)->send(
            new DailySellerCommissionMail(
                seller: $this->seller,
                salesCount: (int) $summary->count,
                totalValue: (float) $summary->total_value,
                totalCommission: (float) $summary->total_commission,
                date: $this->date,
            ),
        );
    }
}
