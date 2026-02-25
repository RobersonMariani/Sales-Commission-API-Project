<?php

declare(strict_types=1);

namespace App\Api\Modules\Seller\Tests\Assertables;

use Illuminate\Testing\Fluent\AssertableJson;

class SellerAssertableJson
{
    public static function schema(AssertableJson $json): AssertableJson
    {
        return $json
            ->whereType('id', 'integer')
            ->whereType('name', 'string')
            ->whereType('email', 'string')
            ->whereType('created_at', ['string', 'null'])
            ->whereType('updated_at', ['string', 'null'])
            ->etc();
    }
}
