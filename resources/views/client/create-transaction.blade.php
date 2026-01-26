<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            New Transaction
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
                <p class="text-gray-300"><strong>Account:</strong> {{ $account->account_number }}</p>
                <p class="text-gray-300"><strong>Current Balance:</strong> <span class="text-green-400 font-semibold">${{ number_format($account->balance, 2) }}</span></p>
            </div>

            <!-- Transaction Form -->
            <div class="bg-gray-800 shadow-md rounded-lg p-6">
                <form method="POST" action="{{ route('client.transaction.store') }}">
                    @csrf

                    <!-- Transaction Type -->
                    <div class="mb-4">
                        <label for="type" class="block text-sm font-medium text-gray-300 mb-2">Transaction Type</label>
                        <div class="flex gap-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="type" value="credit" checked 
                                       class="rounded border-gray-700 bg-gray-700 text-indigo-600 focus:ring-indigo-500">
                                <span class="ms-2 text-gray-300">Deposit (Credit)</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="type" value="debit"
                                       class="rounded border-gray-700 bg-gray-700 text-indigo-600 focus:ring-indigo-500">
                                <span class="ms-2 text-gray-300">Withdraw (Debit)</span>
                            </label>
                        </div>
                        @error('type')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Amount -->
                    <div class="mb-4">
                        <label for="amount" class="block text-sm font-medium text-gray-300">Amount</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-400 sm:text-sm">$</span>
                            </div>
                            <input type="number" 
                                   name="amount" 
                                   id="amount" 
                                   step="0.01"
                                   min="0.01"
                                   value="{{ old('amount') }}"
                                   required
                                   class="block w-full pl-7 rounded-md border-gray-700 bg-gray-700 text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="0.00">
                        </div>
                        @error('amount')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-300">Description</label>
                        <input type="text" 
                               name="description" 
                               id="description" 
                               value="{{ old('description') }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-700 bg-gray-700 text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="e.g., Salary deposit, Grocery shopping, etc.">
                        @error('description')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center gap-4">
                        <button type="submit" 
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Process Transaction
                        </button>
                        <a href="{{ route('client.dashboard') }}" 
                           class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
