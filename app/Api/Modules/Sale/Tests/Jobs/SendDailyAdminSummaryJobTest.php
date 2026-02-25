<?php

declare(strict_types=1);

namespace App\Api\Modules\Sale\Tests\Jobs;

use App\Api\Modules\Sale\Jobs\SendDailyAdminSummaryJob;
use App\Api\Modules\Sale\Mail\DailyAdminSummaryMail;
use App\Models\Sale;
use App\Models\Seller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('sale')]
class SendDailyAdminSummaryJobTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldSendEmailWithCorrectSummaryData(): void
    {
        // Arrange
        Mail::fake();
        $date = '2026-02-24';
        $seller1 = Seller::factory()->create();
        $seller2 = Seller::factory()->create();

        Sale::factory()->create(['seller_id' => $seller1->id, 'value' => 1000.00, 'sale_date' => $date]);
        Sale::factory()->create(['seller_id' => $seller1->id, 'value' => 500.00, 'sale_date' => $date]);
        Sale::factory()->create(['seller_id' => $seller2->id, 'value' => 200.00, 'sale_date' => $date]);

        // Act
        $job = new SendDailyAdminSummaryJob($date);
        $job->handle();

        // Assert
        Mail::assertQueued(DailyAdminSummaryMail::class, function ($mail) {
            return $mail->salesCount === 3
                && $mail->sellersCount === 2
                && $mail->totalSales === 1700.0;
        });
    }

    public function testShouldSendEmailWithZeroTotalsWhenNoSalesOnDate(): void
    {
        // Arrange
        Mail::fake();
        $seller = Seller::factory()->create();

        Sale::factory()->create(['seller_id' => $seller->id, 'sale_date' => '2026-02-20']);

        // Act
        $job = new SendDailyAdminSummaryJob('2026-02-24');
        $job->handle();

        // Assert
        Mail::assertQueued(DailyAdminSummaryMail::class, function ($mail) {
            return $mail->salesCount === 0
                && $mail->sellersCount === 0
                && $mail->totalSales === 0.0;
        });
    }

    public function testShouldSendEmailToAdminConfiguredAddress(): void
    {
        // Arrange
        Mail::fake();

        // Act
        $job = new SendDailyAdminSummaryJob('2026-02-24');
        $job->handle();

        // Assert
        Mail::assertQueued(DailyAdminSummaryMail::class, function ($mail) {
            return $mail->hasTo(config('mail.admin_email'));
        });
    }
}
