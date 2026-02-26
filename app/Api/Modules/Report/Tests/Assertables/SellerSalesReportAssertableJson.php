<?php

namespace App\Api\Modules\Report\Tests\Assertables;

use Illuminate\Testing\Fluent\AssertableJson;

class SellerSalesReportAssertableJson
{
    public static function schema(AssertableJson $json): AssertableJson
    {
        return $json
            ->whereType('seller_id', 'integer')
            ->whereType('seller_name', 'string')
            ->whereType('seller_email', 'string')
            ->whereType('total_sales', 'integer')
            ->whereType('total_value', ['integer', 'double'])
            ->whereType('total_commission', ['integer', 'double'])
            ->etc();
    }
}
