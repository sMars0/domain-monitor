<?php

namespace App\Jobs;

use App\Models\Domain;
use App\Services\Monitoring\DomainCheckService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CheckDomainJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly int $domainId,
    ) {
    }

    public function handle(DomainCheckService $domainCheckService): void
    {
        $domain = Domain::find($this->domainId);

        if ($domain === null || ! $domain->is_active) {
            return;
        }

        $domainCheckService->check($domain);
    }
}
