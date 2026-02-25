<?php

namespace App\Api\Modules\Sale\Tests\Assertables;

use Illuminate\Testing\Fluent\AssertableJson;

class SaleAssertableJson
{
    public static function schema(AssertableJson $json): AssertableJson
    {
        return $json
            ->whereType('id', 'integer')
            ->whereType('seller_id', 'integer')
            ->whereType('value', ['integer', 'double'])
            ->whereType('commission', ['integer', 'double'])
            ->whereType('sale_date', 'string')
            ->whereType('created_at', ['string', 'null'])
            ->whereType('updated_at', ['string', 'null'])
            ->etc();
    }
}
