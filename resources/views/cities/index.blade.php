<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Városok') }}
            </h2>
            @if($isAuthenticated)
            <a href="{{ route('cities.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                {{ __('Új város') }}
            </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($message = Session::get('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ $message }}
                </div>
            @endif

            @if ($message = Session::get('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ $message }}
                </div>
            @endif

            <!-- County Selection and Letter Filter -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="GET" action="{{ route('cities.index') }}" class="mb-4">
                        <div class="mb-4">
                            <label for="county_id" class="block text-gray-700 text-sm font-bold mb-2">
                                {{ __('Megye kiválasztása') }}
                            </label>
                            <select name="county_id" id="county_id" onchange="this.form.submit()" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="">-- Válassz egy megyét --</option>
                                @foreach($counties as $county)
                                <option value="{{ $county->id }}" {{ $selectedCounty == $county->id ? 'selected' : '' }}>
                                    {{ $county->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </form>

                    @if($selectedCounty && count($letters) > 0)
                    <div>
                        <p class="text-gray-700 text-sm font-bold mb-3">{{ __('Városok kezdőbetűi') }}</p>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('cities.index', ['county_id' => $selectedCounty, 'letter' => 'all']) }}" 
                               class="px-3 py-2 rounded font-bold transition {{ $selectedLetter == 'all' ? 'bg-green-500 text-white' : 'bg-green-200 text-green-800 hover:bg-green-300' }}">
                                {{ __('Összes') }}
                            </a>
                            @foreach($letters as $l)
                            <a href="{{ route('cities.index', ['county_id' => $selectedCounty, 'letter' => $l]) }}" 
                               class="px-3 py-2 rounded font-bold transition {{ $selectedLetter == $l ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                                {{ strtoupper($l) }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if($isAuthenticated && $selectedCounty)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <a href="{{ route('cities.export.csv', ['county_id' => $selectedCounty]) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mr-2">
                        {{ __('CSV export') }}
                    </a>
                    <a href="{{ route('cities.export.pdf', ['county_id' => $selectedCounty]) }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        {{ __('PDF export') }}
                    </a>
                </div>
            </div>
            @endif

            <!-- Cities List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(count($cities) > 0)
                    <table class="min-w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-100 border-b">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Város</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Megye</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Irányítószám</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Műveletek</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cities as $city)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $city->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $city->place_name ?? $city->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ (is_object($city->county) && isset($city->county->name)) ? $city->county->name : '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $city->zip_code ?? $city->postal_code }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('cities.show', $city->id) }}" class="text-blue-600 hover:text-blue-900">{{ __('Megtekintés') }}</a>
                                    @if($isAuthenticated)
                                    <a href="{{ route('cities.edit', $city->id) }}" class="text-yellow-600 hover:text-yellow-900 ml-4">{{ __('Szerkesztés') }}</a>
                                    <form method="POST" action="{{ route('cities.destroy', $city->id) }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 ml-4" onclick="return confirm('Biztosan törli?')">{{ __('Törlés') }}</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <p class="text-gray-600">
                        @if($selectedCounty)
                            {{ __('Nincsenek városok ebben a megyében.') }}
                        @else
                            {{ __('Válassz egy megyét a városok megtekintéséhez.') }}
                        @endif
                    </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
