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
            'checked_at'       => $checkedAt,
            'status'           => $status,
            'http_code'        => $result->httpCode,
            'response_time_ms' => $result->responseTimeMs,
            'error_message'    => $result->errorMessage,
        ]);

        $domain->update([
            'last_status'     => $status,
            'last_checked_at' => $checkedAt,
            'check_queued_at' => null,
        ]);

        // Email notifications are a planned bonus feature.
        // To enable: implement App\Notifications\DomainDownNotification
        // and App\Notifications\DomainRecoveredNotification,
        // then call $domain->user->notify(...) in the blocks below.
        // Configure MAIL_* variables in .env (or use a queue channel).
        if ($previousStatus === 'up' && $status === 'down') {
            // $domain->user->notify(new \App\Notifications\DomainDownNotification($domain, $domainCheck));
        }

        if ($previousStatus === 'down' && $status === 'up') {
            // $domain->user->notify(new \App\Notifications\DomainRecoveredNotification($domain, $domainCheck));
        }

        return $domainCheck;
    }
}
