<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Új város') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('cities.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="county_id" class="block text-gray-700 text-sm font-bold mb-2">
                                {{ __('Megye') }}
                            </label>
                            <select name="county_id" id="county_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('county_id') border-red-500 @enderror">
                                <option value="">-- Válassz egy megyét --</option>
                                @foreach($counties as $county)
                                <option value="{{ $county->id }}" {{ old('county_id') == $county->id ? 'selected' : '' }}>
                                    {{ $county->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('county_id')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">
                                {{ __('Város neve') }}
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror">
                            @error('name')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="postal_code" class="block text-gray-700 text-sm font-bold mb-2">
                                {{ __('Irányítószám') }}
                            </label>
                            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('postal_code') border-red-500 @enderror">
                            @error('postal_code')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex gap-4">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                {{ __('Létrehozás') }}
                            </button>
                            <a href="{{ route('cities.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                {{ __('Mégse') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
