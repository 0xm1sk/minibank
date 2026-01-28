<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            User Details - {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Back Link -->
            <div class="mb-6">
                <a href="{{ route('admin.search') }}" class="text-indigo-400 hover:text-indigo-300">
                    ← Back to Users List
                </a>
            </div>

            <!-- User Information Card -->
            <div class="bg-gray-800 shadow-md rounded-lg p-6 mb-6">
                <div class="flex justify-between items-start mb-6">
                    <h3 class="text-xl font-semibold text-gray-200">User Information</h3>
                    <div class="flex space-x-2">
                        @if(Auth::user() && Auth::user()->isAdmin())
                            <a href="{{ route('admin.user.edit', $user->id) }}"
                               class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                                Edit User
                            </a>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <p class="text-gray-400 text-sm">Full Name</p>
                        <p class="font-semibold text-gray-200">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Email Address</p>
                        <p class="font-semibold text-gray-200">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Role</p>
                        <span class="px-2 py-1 rounded text-sm
                            @if($user->role && $user->role->name == 'Admin') bg-red-900 text-red-200
                            @elseif($user->role && str_contains($user->role->name, 'Manager')) bg-blue-900 text-blue-200
                            @elseif($user->role && str_contains($user->role->name, 'Employee')) bg-yellow-900 text-yellow-200
                            @else bg-green-900 text-green-200 @endif">
                            {{ $user->role->name ?? 'No Role' }}
                        </span>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Status</p>
                        <span class="px-2 py-1 rounded text-sm
                            @if($user->status == 'active') bg-green-900 text-green-200
                            @elseif($user->status == 'inactive') bg-gray-900 text-gray-200
                            @else bg-red-900 text-red-200 @endif">
                            {{ ucfirst($user->status ?? 'active') }}
                        </span>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Member Since</p>
                        <p class="font-semibold text-gray-200">{{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Last Updated</p>
                        <p class="font-semibold text-gray-200">{{ $user->updated_at->diffForHumans() }}</p>
                    </div>
                </div>

                @if($user->phone || $user->address || $user->date_of_birth)
                <div class="mt-6 pt-6 border-t border-gray-700">
                    <h4 class="text-lg font-medium text-gray-200 mb-4">Additional Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @if($user->phone)
                        <div>
                            <p class="text-gray-400 text-sm">Phone Number</p>
                            <p class="font-semibold text-gray-200">{{ $user->phone }}</p>
                        </div>
                        @endif
                        @if($user->address)
                        <div>
                            <p class="text-gray-400 text-sm">Address</p>
                            <p class="font-semibold text-gray-200">{{ $user->address }}</p>
                        </div>
                        @endif
                        @if($user->date_of_birth)
                        <div>
                            <p class="text-gray-400 text-sm">Date of Birth</p>
                            <p class="font-semibold text-gray-200">{{ $user->date_of_birth->format('M d, Y') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Account Information (Only for Clients) -->
            @if($user->role && in_array($user->role->id, [1, 2, 3]) && $user->accounts && $user->accounts->count() > 0)
            <div class="bg-gray-800 shadow-md rounded-lg p-6 mb-6">
                <h3 class="text-xl font-semibold text-gray-200 mb-6">Account Information</h3>

                @foreach($user->accounts as $account)
                <div class="bg-gray-700 rounded-lg p-4 {{ !$loop->last ? 'mb-4' : '' }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <p class="text-gray-400 text-sm">Account Number</p>
                            <p class="font-semibold text-gray-200">{{ $account->account_number }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Balance</p>
                            <p class="font-semibold {{ $account->balance >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                ${{ number_format($account->balance, 2) }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Account Type</p>
                            <p class="font-semibold text-gray-200 capitalize">{{ $account->account_type ?? 'Checking' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Status</p>
                            <span class="px-2 py-1 rounded text-xs
                                @if($account->status == 'active') bg-green-900 text-green-200
                                @elseif($account->status == 'inactive') bg-gray-900 text-gray-200
                                @else bg-red-900 text-red-200 @endif">
                                {{ ucfirst($account->status ?? 'active') }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Transaction History (Only for Users with Accounts) -->
            @if(isset($transactions) && $transactions->count() > 0)
            <div class="bg-gray-800 shadow-md rounded-lg overflow-hidden mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-200">Recent Transaction History</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto border-collapse rounded-lg overflow-hidden">
                            <thead>
                                <tr class="bg-gray-700">
                                    <th class="px-4 py-3 text-left border border-gray-600 text-gray-200">Date</th>
                                    <th class="px-4 py-3 text-left border border-gray-600 text-gray-200">Type</th>
                                    <th class="px-4 py-3 text-left border border-gray-600 text-gray-200">Description</th>
                                    <th class="px-4 py-3 text-right border border-gray-600 text-gray-200">Amount</th>
                                    <th class="px-4 py-3 text-center border border-gray-600 text-gray-200">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                    <tr class="hover:bg-gray-700">
                                        <td class="px-4 py-3 border border-gray-600 text-gray-300 text-sm">
                                            {{ $transaction->created_at->format('M d, Y H:i') }}
                                        </td>
                                        <td class="px-4 py-3 border border-gray-600 text-gray-300 capitalize">
                                            {{ str_replace('_', ' ', $transaction->type) }}
                                        </td>
                                        <td class="px-4 py-3 border border-gray-600 text-gray-300">
                                            {{ $transaction->description }}
                                        </td>
                                        <td class="px-4 py-3 text-right border border-gray-600 font-semibold
                                            @if($transaction->type == 'transfer_in' || $transaction->type == 'deposit') text-green-400
                                            @else text-red-400 @endif">
                                            @if($transaction->type == 'transfer_in' || $transaction->type == 'deposit')
                                                +${{ number_format($transaction->amount, 2) }}
                                            @else
                                                -${{ number_format($transaction->amount, 2) }}
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center border border-gray-600">
                                            <span class="px-2 py-1 rounded text-xs
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

                    @if($transactions->hasPages())
                        <div class="mt-6">
                            {{ $transactions->links() }}
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- No Account Message for Non-Clients -->
            @if($user->role && !in_array($user->role->id, [1, 2, 3]))
            <div class="bg-blue-900/20 border border-blue-700 rounded-lg p-6 mb-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-200">
                            <strong>{{ $user->role->name }}</strong> accounts don't have banking accounts. Only client accounts (Regular, VIP, Enterprise) have banking accounts and transaction history.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- User Actions -->
            @if(Auth::user() && Auth::user()->isAdmin())
            <div class="bg-gray-800 shadow-md rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-200 mb-4">User Actions</h3>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('admin.user.edit', $user->id) }}"
                       class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                        Edit User
                    </a>
                    @if(Auth::user() && $user->id != Auth::user()->id)
                        @if($user->status == 'active')
                            <form method="POST" action="{{ route('admin.user.update', $user->id) }}" class="inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="suspended">
                                <input type="hidden" name="name" value="{{ $user->name }}">
                                <input type="hidden" name="email" value="{{ $user->email }}">
                                <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                                <button type="submit"
                                        class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700"
                                        onclick="return confirm('Are you sure you want to suspend this user?');">
                                    Suspend User
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.user.update', $user->id) }}" class="inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="active">
                                <input type="hidden" name="name" value="{{ $user->name }}">
                                <input type="hidden" name="email" value="{{ $user->email }}">
                                <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                                <button type="submit"
                                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                    Activate User
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('admin.user.delete', $user->id) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                                    onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                Delete User
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
