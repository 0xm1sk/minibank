<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-exchange-alt mr-3 text-indigo-600 dark:text-indigo-400"></i>
                    {{ __('Transfer Money') }}
                </h2>
                <p class="mt-2 text-gray-600 dark:text-gray-400">{{ __('Send money to another account securely') }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('client.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    {{ __('Back to Dashboard') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Account Balance Card -->
            <div class="bg-gradient-to-r from-green-500 to-teal-600 rounded-xl shadow-lg p-6 text-white mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-green-100">{{ __('Available Balance') }}</h3>
                        <p class="text-3xl font-bold mt-1">${{ number_format($account->balance, 2) }}</p>
                        <p class="text-sm text-green-100 mt-1">{{ __('Account:') }} {{ $account->account_number }}</p>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm rounded-lg p-3">
                        <i class="fas fa-wallet text-3xl"></i>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Transfer Form -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <i class="fas fa-paper-plane mr-2 text-blue-500"></i>
                            {{ __('Transfer Details') }}
                        </h3>
                    </div>

                    <form action="{{ route('client.transfer.store') }}" method="POST" class="p-6 space-y-6" data-loading>
                        @csrf

                        <!-- Recipient Search -->
                        <div x-data="{
                            searching: false,
                            results: [],
                            selected: null,
                            query: '',
                            showResults: false,
                            searchRecipient() {
                                if (this.query.length < 3) {
                                    this.results = [];
                                    this.showResults = false;
                                    return;
                                }

                                this.searching = true;
                                fetch(`{{ route('client.search.recipient') }}?search=${encodeURIComponent(this.query)}`)
                                    .then(response => response.json())
                                    .then(data => {
                                        this.results = data;
                                        this.showResults = true;
                                        this.searching = false;
                                    })
                                    .catch(() => {
                                        this.searching = false;
                                        this.results = [];
                                    });
                            },
                            selectRecipient(recipient) {
                                this.selected = recipient;
                                this.query = `${recipient.user_name} (${recipient.account_number})`;
                                this.showResults = false;
                                document.getElementById('to_account_number').value = recipient.account_number;
                            },
                            clearSelection() {
                                this.selected = null;
                                this.query = '';
                                this.results = [];
                                this.showResults = false;
                                document.getElementById('to_account_number').value = '';
                            }
                        }">
                            <div>
                                <label for="recipient_search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-search mr-1"></i>
                                    {{ __('Search Recipient') }}
                                </label>
                                <div class="relative">
                                    <input
                                        type="text"
                                        id="recipient_search"
                                        x-model="query"
                                        @input.debounce.300ms="searchRecipient()"
                                        @focus="showResults = results.length > 0"
                                        class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white text-sm"
                                        placeholder="Search by name, email, or account number..."
                                        autocomplete="off">

                                    <!-- Loading Spinner -->
                                    <div x-show="searching" class="absolute right-3 top-3">
                                        <i class="fas fa-spinner animate-spin text-gray-400"></i>
                                    </div>

                                    <!-- Clear Button -->
                                    <button
                                        x-show="selected"
                                        @click="clearSelection()"
                                        type="button"
                                        class="absolute right-3 top-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <i class="fas fa-times"></i>
                                    </button>

                                    <!-- Search Results Dropdown -->
                                    <div x-show="showResults && results.length > 0"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 transform scale-95"
                                         x-transition:enter-end="opacity-100 transform scale-100"
                                         @click.away="showResults = false"
                                         class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 shadow-lg rounded-lg border border-gray-200 dark:border-gray-700 max-h-60 overflow-auto">
                                        <template x-for="recipient in results" :key="recipient.account_number">
                                            <button
                                                type="button"
                                                @click="selectRecipient(recipient)"
                                                class="w-full px-4 py-3 text-left hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-b-0 focus:outline-none focus:bg-indigo-50 dark:focus:bg-indigo-900/20">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                                        <span class="text-white text-sm font-bold" x-text="recipient.user_name.charAt(0)"></span>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="recipient.user_name"></p>
                                                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate" x-text="recipient.user_email"></p>
                                                        <p class="text-xs text-gray-400 dark:text-gray-500" x-text="recipient.account_number"></p>
                                                    </div>
                                                </div>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <!-- Manual Account Number Input (Hidden) -->
                                <input type="hidden" id="to_account_number" name="to_account_number" value="{{ old('to_account_number') }}">

                                @error('to_account_number')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror

                                <!-- Selected Recipient Display -->
                                <div x-show="selected" class="mt-3 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-check-circle text-green-500"></i>
                                        <div>
                                            <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ __('Recipient Selected') }}</p>
                                            <p class="text-xs text-green-600 dark:text-green-300" x-text="selected ? `${selected.user_name} - ${selected.account_number}` : ''"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Alternative: Manual Account Number -->
                            <div x-show="!selected" class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                    {{ __('Or enter account number manually:') }}
                                </p>
                                <input
                                    type="text"
                                    name="manual_account_number"
                                    @input="if($event.target.value) { document.getElementById('to_account_number').value = $event.target.value; }"
                                    class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white text-sm"
                                    placeholder="Enter 10-digit account number"
                                    pattern="[0-9]{10}"
                                    maxlength="10">
                            </div>
                        </div>

                        <!-- Transfer Amount -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-dollar-sign mr-1"></i>
                                {{ __('Transfer Amount') }}
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                                </div>
                                <input
                                    type="number"
                                    name="amount"
                                    id="amount"
                                    min="0.01"
                                    max="{{ $account->balance }}"
                                    step="0.01"
                                    value="{{ old('amount') }}"
                                    class="block w-full pl-7 pr-12 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white text-sm"
                                    placeholder="0.00"
                                    required>
                            </div>
                            <div class="mt-2 flex justify-between items-center">
                                <div class="flex space-x-2">
                                    <button type="button" onclick="setAmount(100)" class="px-3 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600">$100</button>
                                    <button type="button" onclick="setAmount(500)" class="px-3 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600">$500</button>
                                    <button type="button" onclick="setAmount(1000)" class="px-3 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600">$1,000</button>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('Max:') }} ${{ number_format($account->balance, 2) }}
                                </p>
                            </div>
                            @error('amount')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-comment mr-1"></i>
                                {{ __('Description') }} <span class="text-gray-400">({{ __('Optional') }})</span>
                            </label>
                            <textarea
                                name="description"
                                id="description"
                                rows="3"
                                class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white text-sm"
                                placeholder="What's this transfer for?"
                                maxlength="255">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button
                                type="submit"
                                class="flex-1 inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-105 shadow-lg">
                                <i class="fas fa-paper-plane mr-2"></i>
                                {{ __('Send Transfer') }}
                            </button>
                            <a
                                href="{{ route('client.dashboard') }}"
                                class="inline-flex justify-center items-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-base font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                                <i class="fas fa-times mr-2"></i>
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Transfer Information Sidebar -->
                <div class="space-y-6">
                    <!-- Security Notice -->
                    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                <i class="fas fa-shield-alt mr-2 text-green-500"></i>
                                {{ __('Security') }}
                            </h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <div class="flex items-start space-x-3">
                                <i class="fas fa-lock text-green-500 mt-1 text-sm"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Secure Transfer') }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('All transfers are encrypted and secure') }}</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <i class="fas fa-clock text-blue-500 mt-1 text-sm"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Instant Processing') }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Most transfers complete immediately') }}</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <i class="fas fa-receipt text-purple-500 mt-1 text-sm"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Transaction Record') }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Receipt available immediately') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Transfer Limits -->
                    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                                {{ __('Transfer Limits') }}
                            </h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Minimum') }}</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">$0.01</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Daily Limit') }}</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">$10,000</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Approval Required') }}</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">> $50,000</span>
                            </div>
                            <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('Large transfers may require additional approval and could take up to 1 business day.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Recipients -->
                    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                <i class="fas fa-history mr-2 text-gray-500"></i>
                                {{ __('Recent Recipients') }}
                            </h3>
                        </div>
                        <div class="p-6">
                            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">
                                {{ __('No recent recipients') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function setAmount(amount) {
            document.getElementById('amount').value = amount;
        }

        // Form validation
        document.querySelector('form[data-loading]').addEventListener('submit', function(e) {
            const accountNumber = document.getElementById('to_account_number').value;
            const amount = document.getElementById('amount').value;

            if (!accountNumber) {
                e.preventDefault();
                alert('Please select a recipient or enter an account number.');
                return false;
            }

            if (!amount || parseFloat(amount) <= 0) {
                e.preventDefault();
                alert('Please enter a valid amount.');
                return false;
            }

            const maxAmount = {{ $account->balance }};
            if (parseFloat(amount) > maxAmount) {
                e.preventDefault();
                alert('Transfer amount exceeds available balance.');
                return false;
            }

            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner animate-spin mr-2"></i>Processing...';
            submitBtn.disabled = true;

            // Re-enable after 5 seconds as fallback
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 5000);
        });

        // Real-time balance check
        document.getElementById('amount').addEventListener('input', function(e) {
            const amount = parseFloat(e.target.value);
            const maxAmount = {{ $account->balance }};
            const submitBtn = document.querySelector('button[type="submit"]');

            if (amount > maxAmount) {
                e.target.classList.add('border-red-500');
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                e.target.classList.remove('border-red-500');
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        });
    </script>
</x-app-layout>
