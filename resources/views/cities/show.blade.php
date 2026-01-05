<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $entity->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            {{ __('Város ID') }}
                        </label>
                        <p class="text-gray-900">{{ $entity->id }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            {{ __('Város neve') }}
                        </label>
                        <p class="text-gray-900">{{ $entity->name }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            {{ __('Megye') }}
                        </label>
                        <p class="text-gray-900">{{ $entity->county->name ?? '-' }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            {{ __('Irányítószám') }}
                        </label>
                        <p class="text-gray-900">{{ $entity->postal_code }}</p>
                    </div>

                    <div class="flex gap-4">
                        <a href="{{ route('cities.edit', $entity->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            {{ __('Szerkesztés') }}
                        </a>
                        <a href="{{ route('cities.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            {{ __('Vissza') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
