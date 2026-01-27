<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Transfer Money
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
                <h3 class="text-lg font-semibold text-gray-200 mb-2">Your Account</h3>
                @if(auth()->user()->primaryAccount())
                    <p class="text-gray-300"><strong>Account Number:</strong> {{ auth()->user()->primaryAccount()->account_number }}</p>
                    <p class="text-gray-300"><strong>Available Balance:</strong> <span class="text-green-400 font-semibold">${{ number_format($balance, 2) }}</span></p>
                @else
                    <p class="text-gray-400">No account found</p>
                @endif
            </div>

            <!-- Transfer Form -->
            <div class="bg-gray-800 shadow-md rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-200 mb-4">Send Money to Another User</h3>
                
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

                @if($errors->any())
                    <div class="bg-red-900 border border-red-700 text-red-200 px-4 py-3 rounded mb-4">
                        <h4 class="font-semibold mb-2">Please fix the following errors:</h4>
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('client.transfer.store') }}">
                    @csrf

                    <!-- Recipient Email -->
                    <div class="mb-4">
                        <label for="recipient_email" class="block text-sm font-medium text-gray-300 mb-2">Recipient Email</label>
                        <input type="email" 
                               id="recipient_email" 
                               name="recipient_email" 
                               class="block w-full rounded-md bg-gray-700 border-gray-600 text-gray-200 focus:border-indigo-500 focus:ring-indigo-500"
                               required 
                               value="{{ old('recipient_email') }}"
                               placeholder="Enter recipient's email address">
                        @error('recipient_email')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-400">Enter the email address of the person you want to send money to</p>
                    </div>

                    <!-- Amount -->
                    <div class="mb-4">
                        <label for="amount" class="block text-sm font-medium text-gray-300 mb-2">Amount ($)</label>
                        <input type="number" 
                               id="amount" 
                               name="amount" 
                               class="block w-full rounded-md bg-gray-700 border-gray-600 text-gray-200 focus:border-indigo-500 focus:ring-indigo-500"
                               step="0.01" 
                               min="1" 
                               max="{{ $balance }}" 
                               required 
                               value="{{ old('amount') }}">
                        @error('amount')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-400">Maximum available: ${{ number_format($balance, 2) }}</p>
                        <p class="mt-1 text-xs text-yellow-400">⚠️ Transfers over $50,000 require admin approval</p>
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Description (Optional)</label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="3"
                                  class="block w-full rounded-md bg-gray-700 border-gray-600 text-gray-200 focus:border-indigo-500 focus:ring-indigo-500"
                                  placeholder="e.g., Payment for dinner, Birthday gift, etc.">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end">
                        <a href="{{ route('client.dashboard') }}" class="mr-4 text-gray-400 hover:text-gray-200">
                            Cancel
                        </a>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-gray-800">
                            Send Money
                        </button>
                    </div>
                </form>
            </div>

            <!-- Help Section -->
            <div class="bg-gray-800 shadow-md rounded-lg p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-200 mb-3">Transfer Information</h3>
                <ul class="text-sm text-gray-300 space-y-2">
                    <li>• You can only transfer money to users with active accounts</li>
                    <li>• Transfers are instant and cannot be reversed</li>
                    <li>• Both you and the recipient will see the transaction in your history</li>
                    <li>• Make sure to double-check the recipient's email address</li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
