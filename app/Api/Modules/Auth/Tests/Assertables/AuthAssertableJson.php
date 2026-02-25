<?php

declare(strict_types=1);

namespace App\Api\Modules\Auth\Tests\Assertables;

use Illuminate\Testing\Fluent\AssertableJson;

class AuthAssertableJson
{
    public static function schema(AssertableJson $json): AssertableJson
    {
        return $json
            ->whereType('token', 'string')
            ->where('token_type', 'bearer')
            ->whereType('expires_in', 'integer')
            ->etc();
    }
}
