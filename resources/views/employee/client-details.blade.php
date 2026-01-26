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
                <h3 class="text-xl font-semibold mb-4">Client Information</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600">Name:</p>
                        <p class="font-semibold">{{ $client->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Email:</p>
                        <p class="font-semibold">{{ $client->email }}</p>
                    </div>
                    @if($client->account)
                        <div>
                            <p class="text-gray-600">Account Number:</p>
                            <p class="font-semibold">{{ $client->account->account_number }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Current Balance:</p>
                            <p class="font-semibold text-green-600">${{ number_format($client->account->balance, 2) }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Transactions -->
            @if($client->account && $client->account->transactions->count() > 0)
                <div class="bg-gray-800 shadow-md rounded-lg p-6 overflow-x-auto">
                    <h3 class="text-xl font-semibold mb-4">Transaction History</h3>
                    <table class="min-w-full table-auto border-collapse">
                        <thead>
                            <tr class="bg-gray-700">
                                <th class="px-4 py-2 text-left border border-gray-600">Date</th>
                                <th class="px-4 py-2 text-left border border-gray-600">Description</th>
                                <th class="px-4 py-2 text-right border border-gray-600">Credit</th>
                                <th class="px-4 py-2 text-right border border-gray-600">Debit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($client->account->transactions as $transaction)
                                <tr class="hover:bg-gray-700">
                                    <td class="px-4 py-2 border border-gray-600">{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="px-4 py-2 border border-gray-600">{{ $transaction->description }}</td>
                                    <td class="px-4 py-2 text-right border border-gray-600 text-green-600">
                                        {{ $transaction->type == 'credit' ? '$'.number_format($transaction->amount, 2) : '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-right border border-gray-600 text-red-600">
                                        {{ $transaction->type == 'debit' ? '$'.number_format($transaction->amount, 2) : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="bg-white shadow-md rounded-lg p-6">
                    <p class="text-gray-600">No transactions found for this client.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
