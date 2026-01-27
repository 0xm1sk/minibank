<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            📋 All Transaction Requests Management
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Back Link -->
            <div class="mb-4">
                <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('employee.dashboard') }}" class="text-indigo-400 hover:text-indigo-200">
                    ← Back to Dashboard
                </a>
            </div>

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

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-gray-800 p-4 rounded-lg border border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000 2H6a2 2 0 100 4h2a2 2 0 100-4h-.5a1 1 0 000-2H8a2 2 0 012-2h2a2 2 0 012 2v9a2 2 0 01-2 2H6a2 2 0 01-2-2V5z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-400">Total Requests</p>
                            <p class="text-lg font-semibold text-gray-200">{{ $stats['total'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 p-4 rounded-lg border border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-600 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-400">Pending</p>
                            <p class="text-lg font-semibold text-yellow-400">{{ $stats['pending'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 p-4 rounded-lg border border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-400">Approved</p>
                            <p class="text-lg font-semibold text-green-400">{{ $stats['approved'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 p-4 rounded-lg border border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-600 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-400">Rejected</p>
                            <p class="text-lg font-semibold text-red-400">{{ $stats['rejected'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 p-4 rounded-lg border border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-400">Today</p>
                            <p class="text-lg font-semibold text-purple-400">{{ $stats['today'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="bg-gray-800 shadow-md rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-200 mb-4">Filter Requests</h3>
                <form method="GET" action="{{ route('admin.requests') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                        <select name="status" class="w-full rounded-md bg-gray-700 border-gray-600 text-gray-200">
                            <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All Status</option>
                            <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>

                    <!-- Type Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Type</label>
                        <select name="type" class="w-full rounded-md bg-gray-700 border-gray-600 text-gray-200">
                            <option value="all" {{ $type == 'all' ? 'selected' : '' }}>All Types</option>
                            <option value="deposit" {{ $type == 'deposit' ? 'selected' : '' }}>Deposit</option>
                            <option value="withdrawal" {{ $type == 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                            <option value="transfer" {{ $type == 'transfer' ? 'selected' : '' }}>Transfer</option>
                        </select>
                    </div>

                    <!-- Date From -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">From Date</label>
                        <input type="date" name="date_from" value="{{ $date_from }}" 
                               class="w-full rounded-md bg-gray-700 border-gray-600 text-gray-200">
                    </div>

                    <!-- Date To -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">To Date</label>
                        <input type="date" name="date_to" value="{{ $date_to }}" 
                               class="w-full rounded-md bg-gray-700 border-gray-600 text-gray-200">
                    </div>

                    <!-- Submit Button -->
                    <div class="md:col-span-4">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Apply Filters
                        </button>
                        <a href="{{ route('admin.requests') }}" class="ml-2 px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                            Clear Filters
                        </a>
                    </div>
                </form>
            </div>

            <!-- Quick Actions -->
            <div class="flex space-x-4 mb-6">
                <a href="{{ route('admin.pending-requests') }}" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    ⚠️ View Pending Only ({{ $stats['pending'] }})
                </a>
                @if($stats['pending'] > 0)
                    <button onclick="approveAllPending()" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        ✓ Approve All Pending
                    </button>
                @endif
            </div>

            <!-- Requests Table -->
            <div class="bg-gray-800 shadow-md rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-200">
                        All Requests ({{ $requests->total() }} total)
                    </h3>
                    <div class="text-sm text-gray-400">
                        Showing {{ $requests->firstItem() }} to {{ $requests->lastItem() }} of {{ $requests->total() }}
                    </div>
                </div>

                @if($requests->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead>
                                <tr class="bg-gray-700">
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Request ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Requested By</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Details</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @foreach($requests as $request)
                                    <tr class="hover:bg-gray-700">
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-300">
                                            #{{ str_pad($request->id, 6, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-300">
                                            {{ $request->created_at->format('M j, Y H:i') }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                @if($request->type == 'deposit') bg-green-900 text-green-200
                                                @elseif($request->type == 'withdrawal') bg-red-900 text-red-200
                                                @elseif($request->type == 'transfer') bg-blue-900 text-blue-200
                                                @endif">
                                                {{ ucfirst($request->type) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-gray-200">
                                            ${{ number_format($request->amount, 2) }}
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-300">
                                            <div>{{ $request->requestedBy->name }}</div>
                                            <div class="text-xs text-gray-400">{{ $request->requestedBy->email }}</div>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-300">
                                            @if($request->type == 'transfer')
                                                <div class="text-xs">
                                                    @if($request->fromAccount)
                                                        <div>From: {{ $request->fromAccount->account_number }}</div>
                                                    @endif
                                                    @if($request->toAccount)
                                                        <div>To: {{ $request->toAccount->account_number }}</div>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="text-xs">
                                                    @if($request->toAccount)
                                                        Account: {{ $request->toAccount->account_number }}
                                                    @elseif($request->fromAccount)
                                                        Account: {{ $request->fromAccount->account_number }}
                                                    @endif
                                                </div>
                                            @endif
                                            @if($request->description)
                                                <div class="text-xs text-gray-400 mt-1">{{ Str::limit($request->description, 30) }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                @if($request->status == 'pending') bg-yellow-900 text-yellow-200
                                                @elseif($request->status == 'approved') bg-green-900 text-green-200
                                                @elseif($request->status == 'rejected') bg-red-900 text-red-200
                                                @endif">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                            @if($request->approvedBy)
                                                <div class="text-xs text-gray-400 mt-1">
                                                    by {{ $request->approvedBy->name }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                                            @if($request->status == 'pending')
                                                @if(auth()->user()->canApproveAmount($request->amount))
                                                    <form method="POST" action="{{ route('admin.approve-request', $request->id) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="px-3 py-1 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors flex items-center">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                            </svg>
                                                            Approve
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.reject-request', $request->id) }}" class="inline ml-1">
                                                        @csrf
                                                        <input type="hidden" name="rejection_reason" value="Rejected by administrator">
                                                        <button type="submit" 
                                                                class="px-3 py-1 bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors flex items-center">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                            </svg>
                                                            Reject
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="px-3 py-1 bg-gray-600 text-gray-400 text-xs font-medium rounded flex items-center">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                        </svg>
                                                        Limited
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-xs text-gray-400">
                                                    {{ $request->approved_at ? $request->approved_at->format('M j') : '' }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $requests->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-gray-400 mb-4">
                            <svg class="mx-auto h-16 w-16 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-medium text-gray-200 mb-2">No Requests Found</h3>
                        <p class="text-gray-400 mb-4">No transaction requests match your current filters.</p>
                        <a href="{{ route('admin.requests') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Clear Filters
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function approveAllPending() {
            if (confirm('Are you sure you want to approve ALL pending requests? This action cannot be undone.')) {
                // This would need to be implemented as a bulk action
                alert('Bulk approval feature coming soon!');
            }
        }
    </script>
</x-app-layout>
