<?php

declare(strict_types=1);

namespace App\Api\Modules\Sale\Tests\Jobs;

use App\Api\Modules\Sale\Jobs\SendDailySellerCommissionJob;
use App\Api\Modules\Sale\Mail\DailySellerCommissionMail;
use App\Models\Sale;
use App\Models\Seller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('sale')]
class SendDailySellerCommissionJobTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldSendEmailWhenSellerHasSalesOnDate(): void
    {
        // Arrange
        Mail::fake();
        $seller = Seller::factory()->create();
        $date = '2026-02-24';

        Sale::factory()->create([
            'seller_id' => $seller->id,
            'value' => 1000.00,
            'commission' => 85.00,
            'sale_date' => $date,
        ]);
        Sale::factory()->create([
            'seller_id' => $seller->id,
            'value' => 500.00,
            'commission' => 42.50,
            'sale_date' => $date,
        ]);

        // Act
        $job = new SendDailySellerCommissionJob($seller, $date);
        $job->handle();

        // Assert
        Mail::assertQueued(DailySellerCommissionMail::class, function ($mail) use ($seller) {
            return $mail->seller->id === $seller->id
                && $mail->salesCount === 2
                && $mail->totalValue === 1500.0
                && $mail->totalCommission === 127.5;
        });
    }

    public function testShouldNotSendEmailWhenSellerHasNoSalesOnDate(): void
    {
        // Arrange
        Mail::fake();
        $seller = Seller::factory()->create();

        Sale::factory()->create([
            'seller_id' => $seller->id,
            'sale_date' => '2026-02-20',
        ]);

        // Act
        $job = new SendDailySellerCommissionJob($seller, '2026-02-24');
        $job->handle();

        // Assert
        Mail::assertNotQueued(DailySellerCommissionMail::class);
    }

    public function testShouldNotSendEmailForOtherSellers(): void
    {
        // Arrange
        Mail::fake();
        $seller1 = Seller::factory()->create();
        $seller2 = Seller::factory()->create();

        Sale::factory()->create([
            'seller_id' => $seller2->id,
            'sale_date' => '2026-02-24',
        ]);

        // Act
        $job = new SendDailySellerCommissionJob($seller1, '2026-02-24');
        $job->handle();

        // Assert
        Mail::assertNotQueued(DailySellerCommissionMail::class);
    }
}
