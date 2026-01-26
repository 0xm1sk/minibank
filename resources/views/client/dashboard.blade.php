<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            My Account
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="bg-green-900 border border-green-700 text-green-200 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-900 border border-red-700 text-red-200 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('info'))
                <div class="bg-blue-900 border border-blue-700 text-blue-200 px-4 py-3 rounded mb-4">
                    {{ session('info') }}
                </div>
            @endif

            <!-- Account Card -->
            <div class="bg-gray-800 shadow-md rounded-lg p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold mb-2 text-gray-200">Welcome, {{ $user->name }}</h1>
                        @if($account)
                            <p class="text-gray-300 mb-1"><strong>Account Number:</strong> {{ $account->account_number }}</p>
                            <p class="text-gray-300 mb-1"><strong>Current Balance:</strong> <span class="text-green-400 font-semibold">${{ number_format($account->balance, 2) }}</span></p>
                        @else
                            <p class="text-gray-300">No account found.</p>
                        @endif
                    </div>
                    @if($account)
                        <div class="space-x-2">
                            <a href="{{ route('client.transaction.create') }}"
                               class="px-4 py-2 bg-indigo-600 text-sm font-medium rounded-md hover:bg-indigo-700">
                                + New Transaction
                            </a>
                            @if(Route::has('client.transfer.create'))
                                <a href="{{ route('client.transfer.create') }}"
                                   class="px-4 py-2 bg-green-600 text-sm font-medium rounded-md hover:bg-green-700">
                                    Transfer Money
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Transactions Table -->
            @if($account && $recentTransactions && $recentTransactions->count())
            <div class="bg-gray-800 shadow-md rounded-lg p-6 overflow-x-auto">
                <h2 class="text-xl font-semibold mb-4 text-gray-200">Recent Transaction History</h2>
                <table class="min-w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-700">
                            <th class="px-4 py-2 text-left border border-gray-600 text-gray-200">Date</th>
                            <th class="px-4 py-2 text-left border border-gray-600 text-gray-200">Type</th>
                            <th class="px-4 py-2 text-left border border-gray-600 text-gray-200">Description</th>
                            <th class="px-4 py-2 text-right border border-gray-600 text-gray-200">Amount</th>
                            <th class="px-4 py-2 text-right border border-gray-600 text-gray-200">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTransactions as $t)
                            <tr class="hover:bg-gray-700">
                                <td class="px-4 py-2 border border-gray-600 text-gray-300">{{ $t->created_at->format('Y-m-d H:i') }}</td>
                                <td class="px-4 py-2 border border-gray-600 text-gray-300 capitalize">{{ str_replace('_', ' ', $t->type) }}</td>
                                <td class="px-4 py-2 border border-gray-600 text-gray-300">{{ $t->description }}</td>
                                <td class="px-4 py-2 text-right border border-gray-600
                                    @if($t->type == 'transfer_in' || $t->type == 'deposit') text-green-400
                                    @else text-red-400 @endif">
                                    @if($t->type == 'transfer_in' || $t->type == 'deposit')
                                        +${{ number_format($t->amount, 2) }}
                                    @else
                                        -${{ number_format($t->amount, 2) }}
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-right border border-gray-600">
                                    <span class="px-2 py-1 rounded text-sm
                                        @if($t->status == 'completed') bg-green-900 text-green-200
                                        @elseif($t->status == 'pending') bg-yellow-900 text-yellow-200
                                        @else bg-red-900 text-red-200 @endif">
                                        {{ ucfirst($t->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if(Route::has('client.transactions'))
                    <div class="mt-4">
                        <a href="{{ route('client.transactions') }}" class="text-indigo-400 hover:text-indigo-300">
                            View All Transactions →
                        </a>
                    </div>
                @endif
            </div>
            @elseif($account)
                <div class="bg-gray-800 shadow-md rounded-lg p-6">
                    <p class="text-gray-400">No transactions yet. Start by making a deposit or transfer.</p>
                </div>
            @else
                <div class="bg-gray-800 shadow-md rounded-lg p-6">
                    <p class="text-gray-400">No account found. Please contact support.</p>
                </div>
            @endif

            @if(isset($pendingRequests) && $pendingRequests->count() > 0)
            <!-- Pending Requests -->
            <div class="bg-gray-800 shadow-md rounded-lg p-6 mt-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-200">Pending Requests</h2>
                <div class="space-y-3">
                    @foreach($pendingRequests as $request)
                        <div class="bg-gray-700 p-4 rounded border border-gray-600">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-gray-300 capitalize"><strong>{{ $request->type }}</strong></p>
                                    <p class="text-gray-400 text-sm">{{ $request->description }}</p>
                                    @if($request->toAccount && $request->type == 'transfer')
                                        <p class="text-gray-400 text-sm">To: {{ $request->toAccount->user->name }}</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p class="text-gray-200 font-semibold">${{ number_format($request->amount, 2) }}</p>
                                    <p class="text-yellow-400 text-sm">{{ ucfirst($request->status) }}</p>
                                    <p class="text-gray-500 text-xs">{{ $request->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
