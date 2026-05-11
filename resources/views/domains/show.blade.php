<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $domain->name }}
            </h2>

            <a href="{{ route('domains.edit', $domain) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Edit') }}
            </a>
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <dl class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Name') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $domain->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('URL') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <a href="{{ $domain->url }}" class="text-indigo-600 hover:text-indigo-900" target="_blank" rel="noopener noreferrer">{{ $domain->url }}</a>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Method') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $domain->method }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Check Interval') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $domain->check_interval }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Timeout') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $domain->timeout }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Active') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $domain->is_active ? __('Yes') : __('No') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Last Status') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $domain->last_status }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Last Checked') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $domain->last_checked_at?->format('Y-m-d H:i') ?? __('Never') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-semibold text-lg text-gray-800">{{ __('Recent Check History') }}</h3>

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
                                        <td class="px-3 py-3 text-gray-600">{{ $check->checked_at->format('Y-m-d H:i:s') }}</td>
                                        <td class="px-3 py-3 text-gray-600">{{ $check->status }}</td>
                                        <td class="px-3 py-3 text-gray-600">{{ $check->http_code ?? '-' }}</td>
                                        <td class="px-3 py-3 text-gray-600">{{ $check->response_time_ms ?? '-' }}</td>
                                        <td class="px-3 py-3 text-gray-600">{{ $check->error_message ?? '-' }}</td>
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
