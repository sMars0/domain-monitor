<?php

namespace App\Console\Commands;

use App\Jobs\CheckDomainJob;
use App\Models\Domain;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

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
        $now = Carbon::now();
        $queuedBefore = $now->copy()->subMinutes(10);
        $dispatched = 0;

        // Use lazy() to avoid loading all domains into memory at once.
        Domain::query()
            ->where('is_active', true)
            ->where(function ($query) use ($queuedBefore): void {
                $query
                    ->whereNull('check_queued_at')
                    ->orWhere('check_queued_at', '<=', $queuedBefore);
            })
            ->lazy()
            ->each(function (Domain $domain) use ($now, &$dispatched): void {
                if (! $this->domainIsDue($domain, $now)) {
                    return;
                }

                $domain->forceFill([
                    'check_queued_at' => $now,
                ])->save();

                CheckDomainJob::dispatch($domain->id);

                $dispatched++;
            });

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
}
