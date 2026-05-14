<?php

namespace App\Services\Monitoring;

use App\Models\Domain;
use App\Services\Monitoring\Dto\DomainCheckResult;
use Illuminate\Support\Facades\Http;
use Throwable;

class DomainChecker
{
    public function check(Domain $domain): DomainCheckResult
    {
        $startedAt = hrtime(true);

        try {
            $request = Http::timeout($domain->timeout)
                ->maxRedirects(5);

            // For GET requests, stream and discard the body to avoid buffering
            // potentially large responses — we only care about the status code.
            if ($domain->method === 'GET') {
                $request = $request->withoutBody();
            }

            $response = $request->send($domain->method, $domain->url);

            $responseTimeMs = $this->elapsedMilliseconds($startedAt);
            $httpCode = $response->status();

            return new DomainCheckResult(
                isUp: $httpCode >= 200 && $httpCode <= 399,
                httpCode: $httpCode,
                responseTimeMs: $responseTimeMs,
                errorMessage: $httpCode >= 400 ? $response->reason() : null,
            );
        } catch (Throwable $exception) {
            return new DomainCheckResult(
                isUp: false,
                httpCode: null,
                responseTimeMs: $this->elapsedMilliseconds($startedAt),
                errorMessage: $exception->getMessage(),
            );
        }
    }

    private function elapsedMilliseconds(int $startedAt): int
    {
        return (int) round((hrtime(true) - $startedAt) / 1_000_000);
    }
}
