<?php

declare(strict_types=1);

namespace App\Api\Support\Tests;

use App\Api\Support\Traits\EnumTrait;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

enum FakeStatusEnum: string
{
    use EnumTrait;

    case Active = 'active';
    case Inactive = 'inactive';
    case Blocked = 'blocked';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Ativo',
            self::Inactive => 'Inativo',
            self::Blocked => 'Bloqueado',
        };
    }
}

#[Group('support')]
class EnumTraitTest extends TestCase
{
    public function testValuesShouldReturnAllEnumValues(): void
    {
        $result = FakeStatusEnum::values();

        $this->assertEquals(['active', 'inactive', 'blocked'], $result);
    }

    public function testNamesShouldReturnAllEnumNames(): void
    {
        $result = FakeStatusEnum::names();

        $this->assertEquals(['Active', 'Inactive', 'Blocked'], $result);
    }

    public function testToArrayShouldReturnNameValueMap(): void
    {
        $result = FakeStatusEnum::toArray();

        $this->assertEquals([
            'Active' => 'active',
            'Inactive' => 'inactive',
            'Blocked' => 'blocked',
        ], $result);
    }

    public function testValueOfShouldReturnValueByName(): void
    {
        $this->assertEquals('active', FakeStatusEnum::valueOf('Active'));
        $this->assertEquals('inactive', FakeStatusEnum::valueOf('Inactive'));
    }

    public function testValueOfShouldThrowForInvalidName(): void
    {
        $this->expectException(\ValueError::class);

        FakeStatusEnum::valueOf('NonExistent');
    }

    public function testCaseOfShouldReturnEnumInstance(): void
    {
        $result = FakeStatusEnum::caseOf('Active');

        $this->assertSame(FakeStatusEnum::Active, $result);
    }

    public function testCaseOfShouldThrowForInvalidName(): void
    {
        $this->expectException(\ValueError::class);

        FakeStatusEnum::caseOf('NonExistent');
    }

    public function testEqualsShouldMatchWithStringValue(): void
    {
        $status = FakeStatusEnum::Active;

        $this->assertTrue($status->equals('active'));
        $this->assertFalse($status->equals('inactive'));
    }

    public function testEqualsShouldMatchWithEnumInstance(): void
    {
        $status = FakeStatusEnum::Active;

        $this->assertTrue($status->equals(FakeStatusEnum::Active));
        $this->assertFalse($status->equals(FakeStatusEnum::Inactive));
    }

    public function testHasShouldReturnTrueWhenInArray(): void
    {
        $status = FakeStatusEnum::Active;

        $this->assertTrue($status->has([FakeStatusEnum::Active, FakeStatusEnum::Inactive]));
        $this->assertFalse($status->has([FakeStatusEnum::Inactive, FakeStatusEnum::Blocked]));
    }

    public function testCountShouldReturnTotalCases(): void
    {
        $this->assertEquals(3, FakeStatusEnum::count());
    }

    public function testAllShouldCallMethodOnEachCase(): void
    {
        $result = FakeStatusEnum::all('label');

        $this->assertEquals(['Ativo', 'Inativo', 'Bloqueado'], $result);
    }
}
