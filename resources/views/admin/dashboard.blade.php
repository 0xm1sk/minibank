<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Admin Dashboard - User Management
        </h2>
    </x-slot>

    <div class="py-12 padding 15 " >
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">

            <!-- Quick Actions -->
            <div class="mb-6 flex flex-wrap space-x-4">
                <a href="{{ route('admin.users') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                    All Users
                </a>
                <a href="{{ route('admin.user.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    + Create New User
                </a>
                <a href="{{ route('admin.search') }}"
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Search Users
                </a>
                <a href="{{ route('admin.requests') }}"
                   class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 relative">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000 2H6a2 2 0 100 4h2a2 2 0 100 4h-.5a1 1 0 000 2H8a2 2 0 002-2v-1a2 2 0 00-2-2H6a2 2 0 01-2-2V5z" clip-rule="evenodd"/>
                    </svg>
                    All Requests
                    @if(isset($stats) && $stats['pending_requests'] > 0)
                        <span class="ml-2 bg-red-600 text-xs px-2 py-1 rounded-full animate-pulse">{{ $stats['pending_requests'] }}</span>
                    @endif
                </a>
                @if(isset($stats) && $stats['pending_requests'] > 0)
                <a href="{{ route('admin.pending-requests') }}"
                   class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 relative">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    Pending Requests
                    <span class="ml-2 bg-red-800 text-xs px-2 py-1 rounded-full">{{ $stats['pending_requests'] }}</span>
                </a>
                @endif
            </div>

            <!-- Statistics Cards -->
            @if(isset($stats))
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-gray-800 shadow-md rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-200 mb-2">Total Users</h3>
                    <p class="text-3xl font-bold text-indigo-400">{{ number_format($stats['total_users']) }}</p>
                </div>
                <div class="bg-gray-800 shadow-md rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-200 mb-2">Total Clients</h3>
                    <p class="text-3xl font-bold text-green-400">{{ number_format($stats['total_clients']) }}</p>
                </div>
                <div class="bg-gray-800 shadow-md rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-200 mb-2">Total Balance</h3>
                    <p class="text-2xl font-bold text-yellow-400">${{ number_format($stats['total_balance'], 2) }}</p>
                </div>
                <div class="bg-gray-800 shadow-md rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-200 mb-2">Pending Requests</h3>
                    <p class="text-3xl font-bold {{ $stats['pending_requests'] > 0 ? 'text-red-400' : 'text-gray-400' }}">
                        {{ number_format($stats['pending_requests']) }}
                    </p>
                </div>
            </div>
            @endif

            <!-- Pending Requests -->
            @if(isset($pendingRequests) && $pendingRequests->count() > 0)
            <div class="bg-gray-800 shadow-md rounded-lg overflow-hidden mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-200">Pending Transfer Requests</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto border-collapse rounded-lg overflow-hidden">
                            <thead>
                                <tr class="bg-gray-700">
                                    <th class="px-4 py-3 text-left border border-gray-600 text-gray-200">Type</th>
                                    <th class="px-4 py-3 text-left border border-gray-600 text-gray-200">From/To</th>
                                    <th class="px-4 py-3 text-right border border-gray-600 text-gray-200">Amount</th>
                                    <th class="px-4 py-3 text-left border border-gray-600 text-gray-200">Requested</th>
                                    <th class="px-4 py-3 text-center border border-gray-600 text-gray-200">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingRequests as $request)
                                    <tr class="hover:bg-gray-700">
                                        <td class="px-4 py-3 border border-gray-600 text-gray-300 capitalize">{{ $request->type }}</td>
                                        <td class="px-4 py-3 border border-gray-600 text-gray-300">
                                            @if($request->fromAccount && $request->toAccount)
                                                {{ $request->fromAccount->user->name }} → {{ $request->toAccount->user->name }}
                                            @elseif($request->toAccount)
                                                To: {{ $request->toAccount->user->name }}
                                            @else
                                                From: {{ $request->fromAccount->user->name }}
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right border border-gray-600 text-yellow-400 font-semibold">
                                            ${{ number_format($request->amount, 2) }}
                                        </td>
                                        <td class="px-4 py-3 border border-gray-600 text-gray-400 text-sm">
                                            {{ $request->created_at->diffForHumans() }}
                                        </td>
                                        <td class="px-4 py-3 text-center border border-gray-600">
                                            @if(Auth::user()->canApproveAmount($request->amount))
                                            <div class="flex justify-center gap-2">
                                                <form action="{{ route('admin.approve-request', $request->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-400 hover:text-green-300 text-sm">
                                                        Approve
                                                    </button>
                                                </form>
                                                <span class="text-gray-500">|</span>
                                                <button onclick="openRejectModal({{ $request->id }})" class="text-red-400 hover:text-red-300 text-sm">
                                                    Reject
                                                </button>
                                            </div>
                                            @else
                                                <span class="text-yellow-400 text-xs">Requires higher authorization</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Recent Transactions -->
            @if(isset($recentTransactions) && $recentTransactions->count() > 0)
            <div class="bg-gray-800 shadow-md rounded-lg overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-200">Recent Transactions</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto border-collapse rounded-lg overflow-hidden">
                            <thead>
                                <tr class="bg-gray-700">
                                    <th class="px-4 py-3 text-left border border-gray-600 text-gray-200">User</th>
                                    <th class="px-4 py-3 text-left border border-gray-600 text-gray-200">Type</th>
                                    <th class="px-4 py-3 text-right border border-gray-600 text-gray-200">Amount</th>
                                    <th class="px-4 py-3 text-left border border-gray-600 text-gray-200">Date</th>
                                    <th class="px-4 py-3 text-center border border-gray-600 text-gray-200">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $transaction)
                                    <tr class="hover:bg-gray-700">
                                        <td class="px-4 py-3 border border-gray-600 text-gray-300">{{ $transaction->account->user->name }}</td>
                                        <td class="px-4 py-3 border border-gray-600 text-gray-300 capitalize">{{ str_replace('_', ' ', $transaction->type) }}</td>
                                        <td class="px-4 py-3 text-right border border-gray-600
                                            @if($transaction->type == 'transfer_in' || $transaction->type == 'deposit') text-green-400
                                            @else text-red-400 @endif font-semibold">
                                            @if($transaction->type == 'transfer_in' || $transaction->type == 'deposit')
                                                +${{ number_format($transaction->amount, 2) }}
                                            @else
                                                -${{ number_format($transaction->amount, 2) }}
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 border border-gray-600 text-gray-400 text-sm">
                                            {{ $transaction->created_at->diffForHumans() }}
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
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Reject Request Modal -->
    <div id="rejectModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"></div>
            <div class="inline-block align-bottom bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                <h3 class="text-lg leading-6 font-medium text-gray-200" id="modal-title">
                                    Reject Transfer Request
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-400">
                                        Please provide a reason for rejecting this transfer request.
                                    </p>
                                    <div class="mt-4">
                                        <textarea name="rejection_reason" id="rejection_reason" rows="3"
                                                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-600 rounded-md bg-gray-700 text-gray-200"
                                                  placeholder="Enter rejection reason..." required></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Reject Request
                        </button>
                        <button type="button" onclick="closeRejectModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-600 shadow-sm px-4 py-2 bg-gray-800 text-base font-medium text-gray-300 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        function openRejectModal(requestId) {
            const modal = document.getElementById('rejectModal');
            const form = document.getElementById('rejectForm');
            form.action = `/admin/reject-request/${requestId}`;
            modal.classList.remove('hidden');
        }

        function closeRejectModal() {
            const modal = document.getElementById('rejectModal');
            const textarea = document.getElementById('rejection_reason');
            modal.classList.add('hidden');
            textarea.value = '';
        }

        // Close modal when clicking outside
        document.getElementById('rejectModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRejectModal();
            }
        });
    </script>
</x-app-layout>
