<x-app-layout>
    <x-slot name="title">{{ $domain->name }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('domains.index') }}" class="text-gray-400 hover:text-gray-600 text-sm">&larr; {{ __('Domains') }}</a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $domain->name }}
                </h2>
                @php
                    $statusClasses = match($domain->last_status) {
                        'up'   => 'bg-green-100 text-green-700',
                        'down' => 'bg-red-100 text-red-700',
                        default => 'bg-gray-100 text-gray-500',
                    };
                @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses }}">
                    {{ strtoupper($domain->last_status) }}
                </span>
            </div>

            <div class="flex items-center gap-3">
                <form method="POST" action="{{ route('domains.check', $domain) }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        {{ __('Check now') }}
                    </button>
                </form>

                <a href="{{ route('domains.edit', $domain) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Edit') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 text-sm text-gray-700">
                        {{ __(str_replace('-', ' ', session('status'))) }}
                    </div>
                </div>
            @endif

            {{-- Domain details --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <dl class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Name') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $domain->name }}</dd>
                        </div>
                        <div class="lg:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">{{ __('URL') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <a href="{{ $domain->url }}" class="text-indigo-600 hover:text-indigo-900 break-all" target="_blank" rel="noopener noreferrer">{{ $domain->url }}</a>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Method') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">{{ $domain->method }}</span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Check Interval') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $domain->check_interval }}&nbsp;{{ __('min') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Timeout') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $domain->timeout }}&nbsp;{{ __('sec') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Active') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if ($domain->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">{{ __('Yes') }}</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500">{{ __('No') }}</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Last Checked') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $domain->last_checked_at?->format('Y-m-d H:i') ?? __('Never') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Check history --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-semibold text-lg text-gray-800">{{ __('Check History') }}</h3>

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead>
                                <tr class="text-left text-gray-500">
                                    <th class="px-3 py-2 font-medium">{{ __('Checked At') }}</th>
                                    <th class="px-3 py-2 font-medium">{{ __('Status') }}</th>
                                    <th class="px-3 py-2 font-medium">{{ __('HTTP Code') }}</th>
                                    <th class="px-3 py-2 font-medium">{{ __('Response Time') }}</th>
                                    <th class="px-3 py-2 font-medium">{{ __('Error') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($checks as $check)
                                    <tr>
                                        <td class="px-3 py-3 text-gray-600 whitespace-nowrap">{{ $check->checked_at->format('Y-m-d H:i:s') }}</td>
                                        <td class="px-3 py-3">
                                            @php
                                                $checkStatusClasses = match($check->status) {
                                                    'up'   => 'bg-green-100 text-green-700',
                                                    'down' => 'bg-red-100 text-red-700',
                                                    default => 'bg-gray-100 text-gray-500',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $checkStatusClasses }}">
                                                {{ strtoupper($check->status) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 text-gray-600">{{ $check->http_code ?? '—' }}</td>
                                        <td class="px-3 py-3 text-gray-600">
                                            @if ($check->response_time_ms !== null)
                                                {{ $check->response_time_ms }}&nbsp;ms
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 text-gray-600 max-w-xs truncate">{{ $check->error_message ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-6 text-center text-gray-500">
                                            {{ __('No checks yet.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $checks->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
