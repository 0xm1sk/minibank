<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Transaction Approval Center
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
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
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
                            <p class="text-lg font-semibold text-gray-200">{{ $requests->count() }}</p>
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
                            <p class="text-sm font-medium text-gray-400">Approved Today</p>
                            <p class="text-lg font-semibold text-gray-200">{{ \App\Models\TransferRequest::where('status', 'approved')->whereDate('approved_at', today())->count() }}</p>
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
                            <p class="text-sm font-medium text-gray-400">Rejected Today</p>
                            <p class="text-lg font-semibold text-gray-200">{{ \App\Models\TransferRequest::where('status', 'rejected')->whereDate('approved_at', today())->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 p-4 rounded-lg border border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-400">Total Pending Value</p>
                            <p class="text-lg font-semibold text-gray-200">${{ number_format($pendingRequests->sum('amount'), 0) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="bg-gray-800 shadow-md rounded-lg p-6 mb-6">
                <div class="flex space-x-4 border-b border-gray-700">
                    <a href="{{ route('admin.pending-requests') }}" class="pb-2 px-1 border-b-2 border-indigo-500 font-medium text-sm text-indigo-400">
                        All Pending ({{ $pendingRequests->count() }})
                    </a>
                    <a href="#" class="pb-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-400 hover:text-gray-200 hover:border-gray-300">
                        Deposits ({{ $pendingRequests->where('type', 'deposit')->count() }})
                    </a>
                    <a href="#" class="pb-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-400 hover:text-gray-200 hover:border-gray-300">
                        Withdrawals ({{ $pendingRequests->where('type', 'withdrawal')->count() }})
                    </a>
                    <a href="#" class="pb-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-400 hover:text-gray-200 hover:border-gray-300">
                        Transfers ({{ $pendingRequests->where('type', 'transfer')->count() }})
                    </a>
                </div>
            </div>

            <!-- Pending Requests -->
            <div class="bg-gray-800 shadow-md rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-200">
                        Pending Transaction Requests
                        <span class="ml-2 text-sm text-gray-400">($50,000+ requires approval)</span>
                    </h3>
                    <div class="flex items-center space-x-2">
                        <select class="bg-gray-700 border-gray-600 text-gray-200 rounded-md text-sm">
                            <option>Sort by Date (Newest)</option>
                            <option>Sort by Amount (Highest)</option>
                            <option>Sort by Type</option>
                        </select>
                    </div>
                </div>

                @if($requests->count() > 0)
                    <div class="space-y-4">
                        @foreach($requests as $request)
                            <div class="bg-gray-700 p-4 rounded-lg border border-gray-600 hover:border-gray-500 transition-colors">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <!-- Request Header -->
                                        <div class="flex items-center mb-3">
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                                @if($request->type == 'deposit') bg-green-900 text-green-200
                                                @elseif($request->type == 'withdrawal') bg-red-900 text-red-200
                                                @elseif($request->type == 'transfer') bg-blue-900 text-blue-200
                                                @endif">
                                                {{ ucfirst($request->type) }}
                                            </span>
                                            <span class="ml-3 text-xl font-bold text-gray-200">
                                                ${{ number_format($request->amount, 2) }}
                                            </span>
                                            <span class="ml-3 px-2 py-1 bg-yellow-900 text-yellow-200 text-xs rounded-full">
                                                PENDING
                                            </span>
                                        </div>

                                        <!-- Request Details Grid -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-300">
                                            <div>
                                                <p class="mb-1"><strong>Requested by:</strong> {{ $request->requestedBy->name }}</p>
                                                <p class="mb-1"><strong>Email:</strong> {{ $request->requestedBy->email }}</p>
                                                <p class="mb-1"><strong>Date:</strong> {{ $request->created_at->format('M j, Y g:i A') }}</p>
                                                <p class="mb-1"><strong>Request ID:</strong> #{{ str_pad($request->id, 6, '0', STR_PAD_LEFT) }}</p>
                                            </div>
                                            <div>
                                                @if($request->type == 'transfer')
                                                    @if($request->fromAccount)
                                                        <p class="mb-1"><strong>From:</strong> {{ $request->fromAccount->account_number }} ({{ $request->fromAccount->user->name }})</p>
                                                    @endif
                                                    @if($request->toAccount)
                                                        <p class="mb-1"><strong>To:</strong> {{ $request->toAccount->account_number }} ({{ $request->toAccount->user->name }})</p>
                                                    @endif
                                                @elseif($request->type == 'deposit')
                                                    @if($request->toAccount)
                                                        <p class="mb-1"><strong>Account:</strong> {{ $request->toAccount->account_number }} ({{ $request->toAccount->user->name }})</p>
                                                        <p class="mb-1"><strong>Current Balance:</strong> ${{ number_format($request->toAccount->balance, 2) }}</p>
                                                    @endif
                                                @elseif($request->type == 'withdrawal')
                                                    @if($request->fromAccount)
                                                        <p class="mb-1"><strong>Account:</strong> {{ $request->fromAccount->account_number }} ({{ $request->fromAccount->user->name }})</p>
                                                        <p class="mb-1"><strong>Available Balance:</strong> ${{ number_format($request->fromAccount->balance, 2) }}</p>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>

                                        @if($request->description)
                                            <div class="mt-3 p-3 bg-gray-600 rounded">
                                                <p class="text-sm"><strong>Description:</strong> {{ $request->description }}</p>
                                            </div>
                                        @endif

                                        <!-- Approval Status -->
                                        <div class="mt-3 pt-3 border-t border-gray-600">
                                            <div class="flex items-center justify-between">
                                                <p class="text-xs text-gray-400">
                                                    Your approval limit: 
                                                    @if(auth()->user()->canApproveAmount($request->amount))
                                                        <span class="text-green-400 font-semibold">✓ Can approve</span>
                                                    @else
                                                        <span class="text-red-400 font-semibold">✗ Cannot approve (insufficient authority)</span>
                                                    @endif
                                                </p>
                                                <p class="text-xs text-gray-400">
                                                    Waiting for: {{ $request->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="ml-6 flex flex-col space-y-2">
                                        @if(auth()->user()->canApproveAmount($request->amount))
                                            <form method="POST" action="{{ route('admin.approve-request', $request->id) }}" class="block">
                                                @csrf
                                                <button type="submit" 
                                                        class="w-full px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors flex items-center justify-center">
                                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                    Approve
                                                </button>
                                            </form>
                                            
                                            <form method="POST" action="{{ route('admin.reject-request', $request->id) }}" class="block">
                                                @csrf
                                                <input type="hidden" name="rejection_reason" value="Rejected by administrator">
                                                <button type="submit" 
                                                        class="w-full px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors flex items-center justify-center">
                                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                    </svg>
                                                    Reject
                                                </button>
                                            </form>
                                        @else
                                            <div class="w-full px-4 py-2 bg-gray-600 text-gray-400 text-sm font-medium rounded-md text-center cursor-not-allowed flex items-center justify-center">
                                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                                Limited
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-gray-400 mb-4">
                            <svg class="mx-auto h-16 w-16 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-medium text-gray-200 mb-2">No Pending Requests</h3>
                        <p class="text-gray-400 mb-4">All transaction requests have been processed.</p>
                        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('employee.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Return to Dashboard
                        </a>
                    </div>
                @endif
            </div>

            <!-- Info Section -->
            <div class="bg-gray-800 shadow-md rounded-lg p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-200 mb-4">Approval Guidelines</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-300">
                    <div>
                        <h4 class="font-semibold text-gray-200 mb-2">Approval Thresholds</h4>
                        <ul class="space-y-1">
                            <li><span class="text-green-400">Manager:</span> Up to $100,000</li>
                            <li><span class="text-blue-400">Supervisor:</span> Up to $500,000</li>
                            <li><span class="text-purple-400">CEO & Admin:</span> Unlimited</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-200 mb-2">Important Notes</h4>
                        <ul class="space-y-1">
                            <li>• Transactions over $50,000 require approval</li>
                            <li>• Once approved, transactions cannot be reversed</li>
                            <li>• All approvals are logged for audit purposes</li>
                            <li>• Users receive notifications for approval status</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
