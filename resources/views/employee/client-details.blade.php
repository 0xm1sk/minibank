<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Client Details - {{ $client->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Back Link -->
            <div class="mb-4">
                <a href="{{ route('employee.dashboard') }}" class="text-indigo-600 hover:text-indigo-800">
                    ← Back to Clients List
                </a>
            </div>

            <!-- Client Information -->
            <div class="bg-gray-800 shadow-md rounded-lg p-6 mb-6">
                <h3 class="text-xl font-semibold mb-4 text-gray-200">Client Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-400">Name:</p>
                        <p class="font-semibold text-gray-200">{{ $client->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Email:</p>
                        <p class="font-semibold text-gray-200">{{ $client->email }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Role:</p>
                        <p class="font-semibold text-gray-200">{{ $client->getRoleName() }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Status:</p>
                        <p class="font-semibold text-gray-200">{{ $client->getStatusBadge() }}</p>
                    </div>
                    @if($client->accounts && $client->accounts->count() > 0)
                        @foreach($client->accounts as $account)
                            <div>
                                <p class="text-gray-400">Account Number:</p>
                                <p class="font-semibold text-gray-200">{{ $account->account_number }}</p>
                            </div>
                            <div>
                                <p class="text-gray-400">Account Type:</p>
                                <p class="font-semibold text-gray-200">{{ $account->getTypeLabel() }}</p>
                            </div>
                            <div>
                                <p class="text-gray-400">Current Balance:</p>
                                <p class="font-semibold text-green-400">${{ number_format($account->balance, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-400">Account Status:</p>
                                <p class="font-semibold text-gray-200">{{ $account->getStatusBadge() }}</p>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Transactions -->
            @if($client->accounts && $client->accounts->count() > 0)
                @foreach($client->accounts as $account)
                    @if($account->transactions && $account->transactions->count() > 0)
                        <div class="bg-gray-800 shadow-md rounded-lg p-6 overflow-x-auto mb-6">
                            <h3 class="text-xl font-semibold mb-4 text-gray-200">
                                Transaction History - {{ $account->account_number }}
                            </h3>
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
                                    @foreach($account->transactions->sortByDesc('created_at') as $transaction)
                                        <tr class="hover:bg-gray-700">
                                            <td class="px-4 py-2 border border-gray-600 text-gray-300">{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                            <td class="px-4 py-2 border border-gray-600 text-gray-300 capitalize">{{ str_replace('_', ' ', $transaction->type) }}</td>
                                            <td class="px-4 py-2 border border-gray-600 text-gray-300">{{ $transaction->description }}</td>
                                            <td class="px-4 py-2 text-right border border-gray-600
                                                @if($transaction->type == 'transfer_in' || $transaction->type == 'deposit') text-green-400
                                                @else text-red-400 @endif">
                                                @if($transaction->type == 'transfer_in' || $transaction->type == 'deposit')
                                                    +${{ number_format($transaction->amount, 2) }}
                                                @else
                                                    -${{ number_format($transaction->amount, 2) }}
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 text-right border border-gray-600">
                                                <span class="px-2 py-1 rounded text-sm
                                                    @if($transaction->status == 'completed') bg-green-900 text-green-200
                                                    @elseif($transaction->status == 'pending') bg-yellow-900 text-yellow-200
                                                    @else bg-red-900 text-red-200 @endif">
                                                    {{ ucfirst($transaction->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="bg-gray-800 shadow-md rounded-lg p-6 mb-6">
                            <h3 class="text-xl font-semibold mb-4 text-gray-200">
                                Transaction History - {{ $account->account_number }}
                            </h3>
                            <p class="text-gray-400">No transactions found for this account.</p>
                        </div>
                    @endif
                @endforeach
            @else
                <div class="bg-gray-800 shadow-md rounded-lg p-6">
                    <h3 class="text-xl font-semibold mb-4 text-gray-200">Transaction History</h3>
                    <p class="text-gray-400">No accounts or transactions found for this client.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
