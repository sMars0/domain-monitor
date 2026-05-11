<?php

namespace Tests\Feature;

use App\Jobs\CheckDomainJob;
use App\Models\Domain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DomainManualCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_dispatch_manual_check_for_own_domain(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $domain = $this->createDomain($user);

        $response = $this
            ->actingAs($user)
            ->post(route('domains.check', $domain));

        $response
            ->assertRedirect(route('domains.show', $domain))
            ->assertSessionHas('status', 'Domain check has been queued.');

        Queue::assertPushed(CheckDomainJob::class, fn (CheckDomainJob $job): bool => $job->domainId === $domain->id);
    }

    public function test_user_cannot_dispatch_check_for_another_users_domain(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $domain = $this->createDomain($otherUser);

        $this
            ->actingAs($user)
            ->post(route('domains.check', $domain))
            ->assertForbidden();

        Queue::assertNotPushed(CheckDomainJob::class);
    }

    private function createDomain(User $user): Domain
    {
        return Domain::create([
            'user_id' => $user->id,
            'name' => 'Example',
            'url' => 'https://example.com',
            'method' => 'GET',
            'check_interval' => 5,
            'timeout' => 10,
            'is_active' => true,
        ]);
    }
}
