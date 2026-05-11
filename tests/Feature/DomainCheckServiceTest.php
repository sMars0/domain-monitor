<?php

namespace Tests\Feature;

use App\Models\Domain;
use App\Models\User;
use App\Services\Monitoring\DomainCheckService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DomainCheckServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_domain_check_service_creates_domain_check_and_updates_domain_status(): void
    {
        Http::fake([
            '*' => Http::response('', 200),
        ]);

        $user = User::factory()->create();
        $domain = Domain::create([
            'user_id' => $user->id,
            'name' => 'Example',
            'url' => 'https://example.com',
            'method' => 'GET',
            'check_interval' => 5,
            'timeout' => 10,
            'is_active' => true,
            'last_status' => 'down',
            'check_queued_at' => now(),
        ]);

        $domainCheck = app(DomainCheckService::class)->check($domain);

        $this->assertSame($domain->id, $domainCheck->domain_id);
        $this->assertSame('up', $domainCheck->status);
        $this->assertSame(200, $domainCheck->http_code);
        $this->assertNotNull($domainCheck->checked_at);
        $this->assertSame('up', $domain->refresh()->last_status);
        $this->assertNotNull($domain->last_checked_at);
        $this->assertNull($domain->check_queued_at);
        $this->assertDatabaseHas('domain_checks', [
            'domain_id' => $domain->id,
            'status' => 'up',
            'http_code' => 200,
        ]);
    }
}
