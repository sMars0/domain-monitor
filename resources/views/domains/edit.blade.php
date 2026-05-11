<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Domain') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('domains.update', $domain) }}" class="p-6 space-y-6">
                    @csrf
                    @method('PATCH')

                    <div>
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $domain->name)" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label for="url" :value="__('URL')" />
                        <x-text-input id="url" name="url" type="url" class="mt-1 block w-full" :value="old('url', $domain->url)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('url')" />
                    </div>

                    <div>
                        <x-input-label for="method" :value="__('Method')" />
                        <select id="method" name="method" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="GET" @selected(old('method', $domain->method) === 'GET')>GET</option>
                            <option value="HEAD" @selected(old('method', $domain->method) === 'HEAD')>HEAD</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('method')" />
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <x-input-label for="check_interval" :value="__('Check Interval')" />
                            <x-text-input id="check_interval" name="check_interval" type="number" min="1" max="1440" class="mt-1 block w-full" :value="old('check_interval', $domain->check_interval)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('check_interval')" />
                        </div>

                        <div>
                            <x-input-label for="timeout" :value="__('Timeout')" />
                            <x-text-input id="timeout" name="timeout" type="number" min="1" max="60" class="mt-1 block w-full" :value="old('timeout', $domain->timeout)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('timeout')" />
                        </div>
                    </div>

                    <div>
                        <label for="is_active" class="inline-flex items-center">
                            <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('is_active', $domain->is_active))>
                            <span class="ms-2 text-sm text-gray-600">{{ __('Active') }}</span>
                        </label>
                        <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Save') }}</x-primary-button>
                        <a href="{{ route('domains.show', $domain) }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
