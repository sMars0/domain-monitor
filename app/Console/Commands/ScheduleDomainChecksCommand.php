<?php

namespace App\Console\Commands;

use App\Jobs\CheckDomainJob;
use App\Models\Domain;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ScheduleDomainChecksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domains:schedule-checks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch due domain availability checks.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = now();
        $queuedBefore = $now->copy()->subMinutes(10);

        $dueDomains = Domain::query()
            ->where('is_active', true)
            ->where(function ($query) use ($queuedBefore): void {
                $query
                    ->whereNull('check_queued_at')
                    ->orWhere('check_queued_at', '<=', $queuedBefore);
            })
            ->get()
            ->filter(fn (Domain $domain): bool => $this->domainIsDue($domain, $now));

        $dispatched = $this->dispatchDueChecks($dueDomains, $now);

        $this->info("Due domains found: {$dueDomains->count()}");
        $this->info("Jobs dispatched: {$dispatched}");

        return self::SUCCESS;
    }

    private function domainIsDue(Domain $domain, Carbon $now): bool
    {
        return $domain->last_checked_at === null
            || $domain->last_checked_at->lessThanOrEqualTo(
                $now->copy()->subMinutes($domain->check_interval)
            );
    }

    /**
     * @param Collection<int, Domain> $domains
     */
    private function dispatchDueChecks(Collection $domains, Carbon $queuedAt): int
    {
        $dispatched = 0;

        foreach ($domains as $domain) {
            $domain->forceFill([
                'check_queued_at' => $queuedAt,
            ])->save();

            CheckDomainJob::dispatch($domain->id);

            $dispatched++;
        }

        return $dispatched;
    }
}
