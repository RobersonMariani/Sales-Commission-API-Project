<?php

declare(strict_types=1);

namespace App\Api\Modules\Sale\Jobs;

use App\Api\Modules\Sale\Mail\DailyAdminSummaryMail;
use App\Models\Seller;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * Job que envia o e-mail diário com o resumo geral de vendas ao administrador.
 */
class SendDailyAdminSummaryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly string $date,
    ) {}

    /**
     * Calcula o resumo geral de vendas do dia e envia o e-mail ao administrador.
     */
    public function handle(): void
    {
        /** @var object{count: int, total_sales: float} $summary */
        $summary = DB::table('sales')
            ->where('sale_date', $this->date)
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(value), 0) as total_sales')
            ->first();

        $sellersCount = Seller::query()
            ->whereHas('sales', fn ($q) => $q->where('sale_date', $this->date))
            ->count();

        /** @var string $adminEmail */
        $adminEmail = config('mail.admin_email');

        Mail::to($adminEmail)->send(
            new DailyAdminSummaryMail(
                totalSales: (float) $summary->total_sales,
                salesCount: (int) $summary->count,
                sellersCount: $sellersCount,
                date: $this->date,
            ),
        );
    }
}
