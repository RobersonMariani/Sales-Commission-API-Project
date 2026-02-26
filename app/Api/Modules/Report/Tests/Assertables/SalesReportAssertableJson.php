<?php

namespace App\Api\Modules\Report\Tests\Assertables;

use Illuminate\Testing\Fluent\AssertableJson;

class SalesReportAssertableJson
{
    public static function schema(AssertableJson $json): AssertableJson
    {
        return $json
            ->whereType('total_sales', 'integer')
            ->whereType('total_value', ['integer', 'double'])
            ->whereType('total_commission', ['integer', 'double'])
            ->whereType('average_value', ['integer', 'double'])
            ->whereType('average_commission', ['integer', 'double'])
            ->etc();
    }
}
