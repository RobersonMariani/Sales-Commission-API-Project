<?php

namespace App\Api\Modules\Report\Repositories;

use App\Api\Modules\Report\Data\SalesReportQueryData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

/**
 * Repositório responsável pelas queries de agregação dos relatórios de vendas.
 */
class ReportRepository
{
    private const MAX_SELLERS_PER_REPORT = 50;

    /**
     * Retorna as métricas agregadas do relatório geral de vendas.
     */
    public function getSalesSummary(SalesReportQueryData $query): stdClass
    {
        return DB::table('sales')
            ->when($query->startDate, fn ($q) => $q->where('sale_date', '>=', $query->startDate))
            ->when($query->endDate, fn ($q) => $q->where('sale_date', '<=', $query->endDate))
            ->selectRaw('
                COUNT(*) as total_sales,
                COALESCE(SUM(value), 0) as total_value,
                COALESCE(SUM(commission), 0) as total_commission,
                COALESCE(AVG(value), 0) as average_value,
                COALESCE(AVG(commission), 0) as average_commission
            ')
            ->first();
    }

    /**
     * Retorna as métricas agregadas por vendedor.
     */
    public function getSalesBySeller(SalesReportQueryData $query): Collection
    {
        return DB::table('sales')
            ->join('sellers', 'sales.seller_id', '=', 'sellers.id')
            ->when($query->startDate, fn ($q) => $q->where('sales.sale_date', '>=', $query->startDate))
            ->when($query->endDate, fn ($q) => $q->where('sales.sale_date', '<=', $query->endDate))
            ->when($query->sellerId !== null, fn ($q) => $q->where('sales.seller_id', $query->sellerId))
            ->selectRaw('
                sellers.id as seller_id,
                sellers.name as seller_name,
                sellers.email as seller_email,
                COUNT(sales.id) as total_sales,
                COALESCE(SUM(sales.value), 0) as total_value,
                COALESCE(SUM(sales.commission), 0) as total_commission
            ')
            ->groupBy('sellers.id', 'sellers.name', 'sellers.email')
            ->orderByDesc('total_value')
            ->limit(self::MAX_SELLERS_PER_REPORT)
            ->get();
    }
}
