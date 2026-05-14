<x-app-layout>
    <x-slot name="title">{{ __('Domains') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Domains') }}
            </h2>

            <a href="{{ route('domains.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Add Domain') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 text-sm text-gray-700">
                        {{ __(str_replace('-', ' ', session('status'))) }}
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead>
                                <tr class="text-left text-gray-500">
                                    <th class="px-3 py-2 font-medium">{{ __('Name') }}</th>
                                    <th class="px-3 py-2 font-medium">{{ __('URL') }}</th>
                                    <th class="px-3 py-2 font-medium">{{ __('Method') }}</th>
                                    <th class="px-3 py-2 font-medium">{{ __('Interval') }}</th>
                                    <th class="px-3 py-2 font-medium">{{ __('Timeout') }}</th>
                                    <th class="px-3 py-2 font-medium">{{ __('Active') }}</th>
                                    <th class="px-3 py-2 font-medium">{{ __('Status') }}</th>
                                    <th class="px-3 py-2 font-medium">{{ __('Last Checked') }}</th>
                                    <th class="px-3 py-2 font-medium">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($domains as $domain)
                                    <tr>
                                        <td class="px-3 py-3 font-medium text-gray-900">{{ $domain->name }}</td>
                                        <td class="px-3 py-3 text-gray-600">
                                            <a href="{{ $domain->url }}" class="text-indigo-600 hover:text-indigo-900 truncate max-w-xs block" target="_blank" rel="noopener noreferrer">
                                                {{ $domain->url }}
                                            </a>
                                        </td>
                                        <td class="px-3 py-3 text-gray-600">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                                {{ $domain->method }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 text-gray-600">{{ $domain->check_interval }}&nbsp;{{ __('min') }}</td>
                                        <td class="px-3 py-3 text-gray-600">{{ $domain->timeout }}&nbsp;{{ __('sec') }}</td>
                                        <td class="px-3 py-3">
                                            @if ($domain->is_active)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">{{ __('Yes') }}</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500">{{ __('No') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3">
                                            @php
                                                $statusClasses = match($domain->last_status) {
                                                    'up'   => 'bg-green-100 text-green-700',
                                                    'down' => 'bg-red-100 text-red-700',
                                                    default => 'bg-gray-100 text-gray-500',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $statusClasses }}">
                                                {{ strtoupper($domain->last_status) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 text-gray-600 whitespace-nowrap">
                                            {{ $domain->last_checked_at?->format('Y-m-d H:i') ?? __('Never') }}
                                        </td>
                                        <td class="px-3 py-3">
                                            <div class="flex items-center gap-3">
                                                <a href="{{ route('domains.show', $domain) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('View') }}</a>
                                                <a href="{{ route('domains.edit', $domain) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                                                <form method="POST" action="{{ route('domains.check', $domain) }}">
                                                    @csrf
                                                    <button type="submit" class="text-indigo-600 hover:text-indigo-900">
                                                        {{ __('Check now') }}
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('domains.destroy', $domain) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('{{ __('Delete this domain?') }}')">
                                                        {{ __('Delete') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-3 py-6 text-center text-gray-500">
                                            {{ __('No domains yet.') }}
                                            <a href="{{ route('domains.create') }}" class="ml-2 text-indigo-600 hover:text-indigo-900">{{ __('Add one') }}</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $domains->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
