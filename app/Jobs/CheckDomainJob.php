<?php

namespace App\Jobs;

use App\Models\Domain;
use App\Services\Monitoring\DomainCheckService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

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

        if ($domain === null) {
            return;
        }

        if (! $domain->is_active) {
            $this->resetQueuedAt($domain);

            return;
        }

        try {
            $domainCheckService->check($domain);
        } catch (Throwable $exception) {
            $this->resetQueuedAt($domain);

            report($exception);
        }
    }

    private function resetQueuedAt(Domain $domain): void
    {
        $domain->forceFill([
            'check_queued_at' => null,
        ])->save();
    }
}
