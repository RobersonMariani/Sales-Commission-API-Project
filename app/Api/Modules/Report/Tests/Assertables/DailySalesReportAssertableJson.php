<?php

namespace App\Api\Modules\Report\Tests\Assertables;

use Illuminate\Testing\Fluent\AssertableJson;

class DailySalesReportAssertableJson
{
    public static function schema(AssertableJson $json): AssertableJson
    {
        return $json
            ->whereType('date', 'string')
            ->whereType('total_sales', 'integer')
            ->whereType('total_value', ['integer', 'double'])
            ->whereType('total_commission', ['integer', 'double'])
            ->etc();
    }
}
