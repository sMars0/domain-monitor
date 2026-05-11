<?php

namespace Tests\Unit;

use App\Models\Domain;
use App\Services\Monitoring\DomainChecker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DomainCheckerTest extends TestCase
{
    public function test_domain_checker_returns_up_for_fake_200_response(): void
    {
        Http::fake([
            '*' => Http::response('', 200),
        ]);

        $result = (new DomainChecker)->check($this->domain());

        $this->assertTrue($result->isUp);
        $this->assertSame(200, $result->httpCode);
        $this->assertNull($result->errorMessage);
    }

    public function test_domain_checker_returns_up_for_fake_301_response(): void
    {
        Http::fake([
            '*' => Http::response('', 301),
        ]);

        $result = (new DomainChecker)->check($this->domain());

        $this->assertTrue($result->isUp);
        $this->assertSame(301, $result->httpCode);
    }

    public function test_domain_checker_returns_down_for_fake_500_response(): void
    {
        Http::fake([
            '*' => Http::response('', 500),
        ]);

        $result = (new DomainChecker)->check($this->domain());

        $this->assertFalse($result->isUp);
        $this->assertSame(500, $result->httpCode);
    }

    private function domain(): Domain
    {
        return new Domain([
            'name' => 'Example',
            'url' => 'https://example.com',
            'method' => 'GET',
            'check_interval' => 5,
            'timeout' => 10,
            'is_active' => true,
        ]);
    }
}
