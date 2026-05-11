<?php

namespace App\Services\Monitoring;

use App\Models\Domain;
use App\Models\DomainCheck;

class DomainCheckService
{
    public function __construct(
        private readonly DomainChecker $checker,
    ) {
    }

    public function check(Domain $domain): DomainCheck
    {
        $previousStatus = $domain->last_status;
        $checkedAt = now();
        $result = $this->checker->check($domain);
        $status = $result->isUp ? 'up' : 'down';

        $domainCheck = $domain->checks()->create([
            'checked_at' => $checkedAt,
            'status' => $status,
            'http_code' => $result->httpCode,
            'response_time_ms' => $result->responseTimeMs,
            'error_message' => $result->errorMessage,
        ]);

        $domain->update([
            'last_status' => $status,
            'last_checked_at' => $checkedAt,
        ]);

        if ($previousStatus === 'up' && $status === 'down') {
            // TODO: Send a downtime notification.
        }

        if ($previousStatus === 'down' && $status === 'up') {
            // TODO: Send a recovery notification.
        }

        return $domainCheck;
    }
}
