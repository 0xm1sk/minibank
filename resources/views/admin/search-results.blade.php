<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Search Users
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Search Form -->
            <div class="bg-gray-800 shadow-md rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('admin.search') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search Input -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-300 mb-1">Search</label>
                            <input
                                type="text"
                                name="search"
                                id="search"
                                value="{{ $search }}"
                                placeholder="Name, email, or account number..."
                                class="block w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Role Filter -->
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-300 mb-1">Role</label>
                            <select
                                name="role"
                                id="role"
                                class="block w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All Roles</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ $roleFilter == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-300 mb-1">Status</label>
                            <select
                                name="status"
                                id="status"
                                class="block w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All Status</option>
                                <option value="active" {{ $statusFilter == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $statusFilter == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="suspended" {{ $statusFilter == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                        </div>

                        <!-- Search Button -->
                        <div class="flex items-end">
                            <button
                                type="submit"
                                class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-800">
                                Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Search Results -->
            <div class="bg-gray-800 shadow-md rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-200">
                            Search Results
                            @if($users->total() > 0)
                                ({{ $users->total() }} found)
                            @endif
                        </h3>
                        <a href="{{ route('admin.user.create') }}"
                           class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            + Create User
                        </a>
                    </div>

                    @if($users->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-auto border-collapse">
                                <thead>
                                    <tr class="bg-gray-700">
                                        <th class="px-4 py-3 text-left border border-gray-600 text-gray-200">Name</th>
                                        <th class="px-4 py-3 text-left border border-gray-600 text-gray-200">Email</th>
                                        <th class="px-4 py-3 text-left border border-gray-600 text-gray-200">Role</th>
                                        <th class="px-4 py-3 text-left border border-gray-600 text-gray-200">Account Number</th>
                                        <th class="px-4 py-3 text-right border border-gray-600 text-gray-200">Balance</th>
                                        <th class="px-4 py-3 text-center border border-gray-600 text-gray-200">Status</th>
                                        <th class="px-4 py-3 text-center border border-gray-600 text-gray-200">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr class="hover:bg-gray-700">
                                            <td class="px-4 py-3 border border-gray-600 text-gray-300">
                                                {{ $user->name }}
                                            </td>
                                            <td class="px-4 py-3 border border-gray-600 text-gray-300">
                                                {{ $user->email }}
                                            </td>
                                            <td class="px-4 py-3 border border-gray-600">
                                                <span class="px-2 py-1 rounded text-sm
                                                    @if($user->role && $user->role->name == 'Admin') bg-red-900 text-red-200
                                                    @elseif($user->role && str_contains($user->role->name, 'Manager')) bg-blue-900 text-blue-200
                                                    @elseif($user->role && str_contains($user->role->name, 'Employee')) bg-yellow-900 text-yellow-200
                                                    @else bg-green-900 text-green-200 @endif">
                                                    {{ $user->role->name ?? 'No Role' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 border border-gray-600 text-gray-300">
                                                @if($user->accounts && $user->accounts->count() > 0)
                                                    {{ $user->accounts->first()->account_number }}
                                                @else
                                                    <span class="text-gray-500">N/A</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-right border border-gray-600">
                                                @if($user->accounts && $user->accounts->count() > 0)
                                                    <span class="font-semibold {{ $user->accounts->sum('balance') >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                                        ${{ number_format($user->accounts->sum('balance'), 2) }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-500">N/A</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center border border-gray-600">
                                                <span class="px-2 py-1 rounded text-xs
                                                    @if($user->status == 'active') bg-green-900 text-green-200
                                                    @elseif($user->status == 'inactive') bg-gray-900 text-gray-200
                                                    @else bg-red-900 text-red-200 @endif">
                                                    {{ ucfirst($user->status ?? 'active') }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-center border border-gray-600">
                                                <div class="flex justify-center gap-2">
                                                    <a href="{{ route('admin.user.details', $user->id) }}"
                                                       class="text-indigo-400 hover:text-indigo-300 text-sm">
                                                        View
                                                    </a>
                                                    <span class="text-gray-500">|</span>
                                                    <a href="{{ route('admin.user.edit', $user->id) }}"
                                                       class="text-yellow-400 hover:text-yellow-300 text-sm">
                                                        Edit
                                                    </a>
                                                    @if($user->id != Auth::user()->id)
                                                        <span class="text-gray-500">|</span>
                                                        <form method="POST" action="{{ route('admin.user.delete', $user->id) }}"
                                                              class="inline"
                                                              onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-400 hover:text-red-300 text-sm">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($users->hasPages())
                            <div class="mt-6">
                                {{ $users->appends(request()->query())->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-400 mb-4">
                                <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-300 mb-2">No users found</h3>
                            <p class="text-gray-400 mb-4">
                                @if($search || $roleFilter || $statusFilter)
                                    No users match your search criteria. Try adjusting your filters.
                                @else
                                    Use the search form above to find users.
                                @endif
                            </p>
                            @if($search || $roleFilter || $statusFilter)
                                <a href="{{ route('admin.search') }}" class="text-indigo-400 hover:text-indigo-300">
                                    Clear all filters
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
