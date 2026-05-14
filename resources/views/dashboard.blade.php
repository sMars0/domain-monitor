<x-app-layout>
    <x-slot name="title">{{ __('Dashboard') }}</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Stats cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</div>
                    <div class="mt-1 text-sm text-gray-500">{{ __('Total Domains') }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center border-t-4 border-green-500">
                    <div class="text-3xl font-bold text-green-600">{{ $stats['up'] }}</div>
                    <div class="mt-1 text-sm text-gray-500">{{ __('Online') }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center border-t-4 border-red-500">
                    <div class="text-3xl font-bold text-red-600">{{ $stats['down'] }}</div>
                    <div class="mt-1 text-sm text-gray-500">{{ __('Offline') }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center border-t-4 border-gray-300">
                    <div class="text-3xl font-bold text-gray-500">{{ $stats['unknown'] }}</div>
                    <div class="mt-1 text-sm text-gray-500">{{ __('Not Yet Checked') }}</div>
                </div>
            </div>

            {{-- Down domains alert --}}
            @if ($downDomains->isNotEmpty())
                <div class="bg-red-50 border border-red-200 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="font-semibold text-red-700 mb-4">{{ __('Offline Domains') }}</h3>
                        <ul class="space-y-2">
                            @foreach ($downDomains as $domain)
                                <li class="flex items-center justify-between">
                                    <div>
                                        <a href="{{ route('domains.show', $domain) }}"
                                           class="font-medium text-red-800 hover:text-red-600">
                                            {{ $domain->name }}
                                        </a>
                                        <span class="ml-2 text-sm text-red-500">{{ $domain->url }}</span>
                                    </div>
                                    <span class="text-xs text-red-400">
                                        {{ $domain->last_checked_at?->diffForHumans() ?? __('Never') }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- Quick link --}}
            @if ($stats['total'] === 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <p class="text-gray-500 mb-4">{{ __('No domains are being monitored yet.') }}</p>
                        <a href="{{ route('domains.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Add Your First Domain') }}
                        </a>
                    </div>
                </div>
            @else
                <div class="text-right">
                    <a href="{{ route('domains.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('View All Domains') }}
                    </a>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
