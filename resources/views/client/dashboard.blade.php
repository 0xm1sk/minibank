<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            My Account
        </h2>
    </x-slot>

    <div class="py-6 sm:py-8 lg:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Account Card -->
            <div class="bg-gray-800 shadow-md rounded-lg p-4 sm:p-6 mb-6">
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
                        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                            <a href="{{ route('client.deposit.form') }}"
                               class="px-3 sm:px-4 py-2 bg-indigo-600 text-sm font-medium rounded-md hover:bg-indigo-700 text-center">
                                + Deposit
                            </a>
                            <a href="{{ route('client.withdraw.form') }}"
                               class="px-3 sm:px-4 py-2 bg-red-600 text-sm font-medium rounded-md hover:bg-red-700 text-center">
                                Withdraw
                            </a>
                            <a href="{{ route('client.transfer.form') }}"
                               class="px-3 sm:px-4 py-2 bg-green-600 text-sm font-medium rounded-md hover:bg-green-700 text-center">
                                Transfer Money
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Transactions Table -->
            @if($account && $recentTransactions && $recentTransactions->count())
            <div class="bg-gray-800 shadow-md rounded-lg p-4 sm:p-6 overflow-x-auto">
                <h2 class="text-xl font-semibold mb-4 text-gray-200">Recent Transaction History</h2>
                <table class="min-w-full table-auto border-collapse rounded-lg overflow-hidden">
                    <thead>
                        <tr class="bg-gray-700">
                            <th class="px-2 sm:px-4 py-2 text-left border border-gray-600 text-gray-200 text-xs sm:text-sm">Date</th>
                            <th class="px-2 sm:px-4 py-2 text-left border border-gray-600 text-gray-200 text-xs sm:text-sm">Type</th>
                            <th class="px-2 sm:px-4 py-2 text-left border border-gray-600 text-gray-200 text-xs sm:text-sm">Description</th>
                            <th class="px-2 sm:px-4 py-2 text-right border border-gray-600 text-gray-200 text-xs sm:text-sm">Amount</th>
                            <th class="px-2 sm:px-4 py-2 text-right border border-gray-600 text-gray-200 text-xs sm:text-sm">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTransactions as $t)
                            <tr class="hover:bg-gray-700">
                                <td class="px-2 sm:px-4 py-2 border border-gray-600 text-gray-300 text-xs sm:text-sm">{{ $t->created_at->format('Y-m-d H:i') }}</td>
                                <td class="px-2 sm:px-4 py-2 border border-gray-600 text-gray-300 capitalize text-xs sm:text-sm">{{ str_replace('_', ' ', $t->type) }}</td>
                                <td class="px-2 sm:px-4 py-2 border border-gray-600 text-gray-300 text-xs sm:text-sm">{{ $t->description }}</td>
                                <td class="px-2 sm:px-4 py-2 text-right border border-gray-600 text-xs sm:text-sm
                                    @if($t->type == 'transfer_in' || $t->type == 'deposit') text-green-400
                                    @elseif($t->type == 'transfer_out' || $t->type == 'withdrawal') text-red-400
                                    @endif text-white font-semibold">
                                    @if($t->type == 'transfer_in' || $t->type == 'deposit') +
                                    @endif ${{ number_format($t->amount, 2) }}
                                </td>
                                <td class="px-2 sm:px-4 py-2 text-right border border-gray-600 text-xs sm:text-sm">
                                    @if($t->status == 'completed')
                                        <span class="px-2 py-1 bg-green-900 text-green-300 rounded-full text-xs">Completed</span>
                                    @elseif($t->status == 'pending')
                                        <span class="px-2 py-1 bg-yellow-900 text-yellow-300 rounded-full text-xs">Pending</span>
                                    @elseif($t->status == 'failed')
                                        <span class="px-2 py-1 bg-red-900 text-red-300 rounded-full text-xs">Failed</span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-700 text-gray-300 rounded-full text-xs">{{ $t->status }}</span>
                                    @endif
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
