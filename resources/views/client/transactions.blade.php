<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Transaction History
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Back Link -->
            <div class="mb-4">
                <a href="{{ route('client.dashboard') }}" class="text-indigo-400 hover:text-indigo-200">
                    ← Back to Dashboard
                </a>
            </div>

            <!-- Account Info -->
            <div class="bg-gray-800 shadow-md rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-200 mb-2">Account Information</h3>
                @if($user->primaryAccount())
                    <p class="text-gray-300"><strong>Account Number:</strong> {{ $user->primaryAccount()->account_number }}</p>
                    <p class="text-gray-300"><strong>Current Balance:</strong> <span class="text-green-400 font-semibold">${{ number_format($user->primaryAccount()->balance, 2) }}</span></p>
                @else
                    <p class="text-gray-400">No account found</p>
                @endif
            </div>

            <!-- Transactions Table -->
            <div class="bg-gray-800 shadow-md rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-200 mb-4">All Transactions</h3>
                
                @if($transactions->count() > 0)
                    <div class="overflow-x-auto">
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
                                @foreach($transactions as $transaction)
                                    <tr class="hover:bg-gray-700">
                                        <td class="px-4 py-2 border border-gray-600 text-gray-300">{{ $transaction['created_at']->format('Y-m-d H:i') }}</td>
                                        <td class="px-4 py-2 border border-gray-600 text-gray-300 capitalize">{{ str_replace('_', ' ', $transaction['type']) }}</td>
                                        <td class="px-4 py-2 border border-gray-600 text-gray-300">{{ $transaction['description'] }}</td>
                                        <td class="px-4 py-2 text-right border border-gray-600
                                            @if($transaction['type'] == 'transfer_in' || $transaction['type'] == 'deposit') text-green-400
                                            @else text-red-400 @endif">
                                            @if($transaction['type'] == 'transfer_in' || $transaction['type'] == 'deposit')
                                                +${{ number_format($transaction['amount'], 2) }}
                                            @else
                                                -${{ number_format($transaction['amount'], 2) }}
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-right border border-gray-600">
                                            <span class="px-2 py-1 rounded text-sm
                                                @if($transaction['status'] == 'completed') bg-green-900 text-green-200
                                                @elseif($transaction['status'] == 'pending') bg-yellow-900 text-yellow-200
                                                @elseif($transaction['status'] == 'failed' && str_contains($transaction['description'], 'REJECTED:')) bg-red-900 text-red-200
                                                @elseif($transaction['status'] == 'failed') bg-red-900 text-red-200
                                                @else bg-red-900 text-red-200 @endif">
                                                @if($transaction['status'] == 'failed' && str_contains($transaction['description'], 'REJECTED:'))
                                                    Rejected
                                                @else
                                                    {{ ucfirst($transaction['status']) }}
                                                @endif
                                            </span>
                                            @if($transaction['status'] == 'failed' && str_contains($transaction['description'], 'REJECTED:'))
                                                <div class="text-xs text-gray-400 mt-1">
                                                    {{ substr($transaction['description'], strpos($transaction['description'], 'REJECTED:') + 9) }}
                                                </div>
                                            @endif
                                            @if($transaction['source'] == 'transfer_request')
                                                <div class="text-xs text-gray-400 mt-1">
                                                    Pending approval
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $transactions->links() }}
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-400">No transactions found.</p>
                        <p class="text-gray-500 text-sm mt-2">Start by making a deposit or transfer.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
