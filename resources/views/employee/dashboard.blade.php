<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Employee Dashboard - All Clients
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search Form -->
            <div class="bg-gray-800 shadow-md rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('employee.search') }}" class="flex gap-4">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ $search ?? '' }}"
                        placeholder="Search by name or email..." 
                        class="flex-1 rounded-md border-gray-700 bg-gray-700 text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Search
                    </button>
                    @if(isset($search))
                        <a href="{{ route('employee.dashboard') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                            Clear
                        </a>
                    @endif
                </form>
            </div>

            <!-- Clients List -->
            <div class="bg-gray-800 shadow-md rounded-lg overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-200">Clients List (Read-Only)</h3>
                    
                    @if($clients->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-auto border-collapse">
                                <thead>
                                    <tr class="bg-gray-700">
                                        <th class="px-4 py-3 text-left border border-gray-600 text-gray-200">Name</th>
                                        <th class="px-4 py-3 text-left border border-gray-600 text-gray-200">Email</th>
                                        <th class="px-4 py-3 text-left border border-gray-600 text-gray-200">Account Number</th>
                                        <th class="px-4 py-3 text-right border border-gray-600 text-gray-200">Balance</th>
                                        <th class="px-4 py-3 text-center border border-gray-600 text-gray-200">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($clients as $client)
                                        <tr class="hover:bg-gray-700">
                                            <td class="px-4 py-3 border border-gray-600 text-gray-300">{{ $client->name }}</td>
                                            <td class="px-4 py-3 border border-gray-600 text-gray-300">{{ $client->email }}</td>
                                            <td class="px-4 py-3 border border-gray-600 text-gray-300">
                                                {{ $client->account ? $client->account->account_number : 'No account' }}
                                            </td>
                                            <td class="px-4 py-3 text-right border border-gray-600">
                                                @if($client->account)
                                                    <span class="font-semibold {{ $client->account->balance >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                                        ${{ number_format($client->account->balance, 2) }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">N/A</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center border border-gray-600">
                                                <a href="{{ route('employee.client.details', $client->id) }}" 
                                                   class="text-indigo-400 hover:text-indigo-300">
                                                    View Details
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-400">No clients found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
