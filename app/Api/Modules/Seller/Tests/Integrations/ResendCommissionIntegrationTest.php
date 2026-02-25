<?php

declare(strict_types=1);

namespace App\Api\Modules\Seller\Tests\Integrations;

use App\Api\Modules\Sale\Jobs\SendDailySellerCommissionJob;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('seller')]
class ResendCommissionIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/sellers/%d/resend-commission';

    public function testShouldReturnSuccessAndDispatchJobWhenSellerExists(): void
    {
        // Arrange
        Queue::fake();
        $user = User::factory()->create();
        $seller = Seller::factory()->create();
        $token = auth('api')->login($user);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->post(sprintf(self::ENDPOINT, $seller->id))
            ->assertOk()
            ->assertJson(['message' => 'Commission email queued successfully.']);

        Queue::assertPushed(SendDailySellerCommissionJob::class);
    }

    public function testShouldDispatchJobWithProvidedDateWhenDateParamSent(): void
    {
        // Arrange
        Queue::fake();
        $user = User::factory()->create();
        $seller = Seller::factory()->create();
        $token = auth('api')->login($user);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->post(sprintf(self::ENDPOINT, $seller->id), ['date' => '2026-01-15'])
            ->assertOk();

        Queue::assertPushed(SendDailySellerCommissionJob::class, function ($job) use ($seller) {
            return $job->seller->id === $seller->id && $job->date === '2026-01-15';
        });
    }

    public function testShouldReturnNotFoundWhenSellerDoesNotExist(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->post(sprintf(self::ENDPOINT, 99999))
            ->assertNotFound();
    }

    public function testShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Arrange
        $seller = Seller::factory()->create();

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->post(sprintf(self::ENDPOINT, $seller->id))
            ->assertUnauthorized();
    }
}
