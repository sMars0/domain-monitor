<?php

namespace Tests\Feature;

use App\Jobs\CheckDomainJob;
use App\Models\Domain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ScheduleDomainChecksCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_dispatches_jobs_for_active_domains_with_last_checked_at_null(): void
    {
        Queue::fake();

        $domain = $this->createDomain([
            'last_checked_at' => null,
        ]);

        $this->artisan('domains:schedule-checks')
            ->expectsOutput('Due domains found: 1')
            ->expectsOutput('Jobs dispatched: 1')
            ->assertSuccessful();

        Queue::assertPushed(CheckDomainJob::class, fn (CheckDomainJob $job): bool => $job->domainId === $domain->id);
        $this->assertNotNull($domain->refresh()->check_queued_at);
    }

    public function test_command_dispatches_jobs_for_active_domains_whose_interval_has_passed(): void
    {
        Queue::fake();

        $domain = $this->createDomain([
            'check_interval' => 5,
            'last_checked_at' => now()->subMinutes(6),
        ]);

        $this->artisan('domains:schedule-checks')->assertSuccessful();

        Queue::assertPushed(CheckDomainJob::class, fn (CheckDomainJob $job): bool => $job->domainId === $domain->id);
    }

    public function test_command_does_not_dispatch_jobs_for_inactive_domains(): void
    {
        Queue::fake();

        $this->createDomain([
            'is_active' => false,
            'last_checked_at' => null,
        ]);

        $this->artisan('domains:schedule-checks')->assertSuccessful();

        Queue::assertNotPushed(CheckDomainJob::class);
    }

    public function test_command_does_not_dispatch_jobs_for_domains_checked_recently(): void
    {
        Queue::fake();

        $this->createDomain([
            'check_interval' => 5,
            'last_checked_at' => now()->subMinutes(4),
        ]);

        $this->artisan('domains:schedule-checks')->assertSuccessful();

        Queue::assertNotPushed(CheckDomainJob::class);
    }

    public function test_command_does_not_dispatch_jobs_if_check_queued_at_is_recent(): void
    {
        Queue::fake();

        $this->createDomain([
            'last_checked_at' => null,
            'check_queued_at' => now()->subMinutes(9),
        ]);

        $this->artisan('domains:schedule-checks')->assertSuccessful();

        Queue::assertNotPushed(CheckDomainJob::class);
    }

    public function test_command_dispatches_again_if_check_queued_at_is_older_than_ten_minutes(): void
    {
        Queue::fake();

        $domain = $this->createDomain([
            'last_checked_at' => null,
            'check_queued_at' => now()->subMinutes(11),
        ]);

        $this->artisan('domains:schedule-checks')->assertSuccessful();

        Queue::assertPushed(CheckDomainJob::class, fn (CheckDomainJob $job): bool => $job->domainId === $domain->id);
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function createDomain(array $overrides = []): Domain
    {
        $user = User::factory()->create();

        return Domain::create(array_merge([
            'user_id' => $user->id,
            'name' => 'Example',
            'url' => 'https://example.com',
            'method' => 'GET',
            'check_interval' => 5,
            'timeout' => 10,
            'is_active' => true,
            'last_status' => 'unknown',
        ], $overrides));
    }
}
