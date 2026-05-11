<?php

namespace Tests\Feature;

use App\Jobs\CheckDomainJob;
use App\Models\Domain;
use App\Models\User;
use App\Services\Monitoring\DomainCheckService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CheckDomainJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_check_domain_job_skips_inactive_domain(): void
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
            'is_active' => false,
            'last_status' => 'unknown',
        ]);

        (new CheckDomainJob($domain->id))->handle(app(DomainCheckService::class));

        $this->assertDatabaseMissing('domain_checks', [
            'domain_id' => $domain->id,
        ]);
        $this->assertSame('unknown', $domain->refresh()->last_status);
    }
}
