<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDomainRequest;
use App\Http\Requests\UpdateDomainRequest;
use App\Jobs\CheckDomainJob;
use App\Models\Domain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DomainController extends Controller
{
    public function index(Request $request): View
    {
        $domains = $request->user()
            ->domains()
            ->latest()
            ->paginate(15);

        return view('domains.index', [
            'domains' => $domains,
        ]);
    }

    public function create(): View
    {
        return view('domains.create');
    }

    public function store(StoreDomainRequest $request): RedirectResponse
    {
        $domain = $request->user()->domains()->create($request->validated());

        return redirect()
            ->route('domains.show', $domain)
            ->with('status', 'domain-created');
    }

    public function show(Domain $domain): View
    {
        $this->authorize('view', $domain);

        $checks = $domain->checks()
            ->latest('checked_at')
            ->paginate(20);

        return view('domains.show', [
            'domain' => $domain,
            'checks' => $checks,
        ]);
    }

    public function edit(Domain $domain): View
    {
        $this->authorize('update', $domain);

        return view('domains.edit', [
            'domain' => $domain,
        ]);
    }

    public function update(UpdateDomainRequest $request, Domain $domain): RedirectResponse
    {
        $this->authorize('update', $domain);

        $domain->update($request->validated());

        return redirect()
            ->route('domains.show', $domain)
            ->with('status', 'domain-updated');
    }

    public function destroy(Domain $domain): RedirectResponse
    {
        $this->authorize('delete', $domain);

        $domain->delete();

        return redirect()
            ->route('domains.index')
            ->with('status', 'domain-deleted');
    }

    public function check(Domain $domain): RedirectResponse
    {
        $this->authorize('check', $domain);

        CheckDomainJob::dispatch($domain->id);

        return redirect()
            ->route('domains.show', $domain)
            ->with('status', 'domain-check-queued');
    }
}
