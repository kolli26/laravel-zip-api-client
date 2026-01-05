<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Megyék') }}
            </h2>
            @if($isAuthenticated)
            <a href="{{ route('counties.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                {{ __('Új megye') }}
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="GET" action="{{ route('counties.index') }}" class="flex gap-2">
                        <input type="text" name="needle" placeholder="Keresés..." value="{{ request()->get('needle') }}" class="flex-1 px-4 py-2 border border-gray-300 rounded">
                        <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Keresés') }}
                        </button>
                        <a href="{{ route('counties.index') }}" class="bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                            {{ __('Törlés') }}
                        </a>
                    </form>
                </div>
            </div>

            @if($isAuthenticated)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <a href="{{ route('counties.export.csv') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mr-2">
                        {{ __('CSV export') }}
                    </a>
                    <a href="{{ route('counties.export.pdf') }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        {{ __('PDF export') }}
                    </a>
                </div>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(count($entities) > 0)
                    <table class="min-w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-100 border-b">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Megye</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Műveletek</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entities as $county)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $county->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $county->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('counties.show', $county->id) }}" class="text-blue-600 hover:text-blue-900">{{ __('Megtekintés') }}</a>
                                    @if($isAuthenticated)
                                    <a href="{{ route('counties.edit', $county->id) }}" class="text-yellow-600 hover:text-yellow-900 ml-4">{{ __('Szerkesztés') }}</a>
                                    <form method="POST" action="{{ route('counties.destroy', $county->id) }}" style="display:inline;">
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
                    <p class="text-gray-600">{{ __('Nincsenek megyék.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
