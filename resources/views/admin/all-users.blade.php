<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            All Users - Admin Management
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Quick Actions -->
            <div class="mb-6 flex space-x-4">
                <a href="{{ route('admin.user.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    + Create New User
                </a>
                <a href="{{ route('admin.search') }}"
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Search Users
                </a>
            </div>

            <!-- Users Table -->
            <div class="bg-gray-800 shadow-md rounded-lg overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-200">All Users ({{ $users->count() }})</h3>

                    @if($users->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-auto border-collapse rounded-lg overflow-hidden">
                                <thead>
                                    <tr class="bg-gray-700">
                                        <th class="px-4 py-3 text-left border border-gray-600 text-gray-200">ID</th>
                                        <th class="px-4 py-3 text-left border border-gray-600 text-gray-200">Name</th>
                                        <th class="px-4 py-3 text-left border border-gray-600 text-gray-200">Email</th>
                                        <th class="px-4 py-3 text-left border border-gray-600 text-gray-200">Role</th>
                                        <th class="px-4 py-3 text-right border border-gray-600 text-gray-200">Balance</th>
                                        <th class="px-4 py-3 text-center border border-gray-600 text-gray-200">Status</th>
                                        <th class="px-4 py-3 text-center border border-gray-600 text-gray-200">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr class="hover:bg-gray-700">
                                            <td class="px-4 py-3 border border-gray-600 text-gray-300">{{ $user->id }}</td>
                                            <td class="px-4 py-3 border border-gray-600 text-gray-300">{{ $user->name }}</td>
                                            <td class="px-4 py-3 border border-gray-600 text-gray-300">{{ $user->email }}</td>
                                            <td class="px-4 py-3 border border-gray-600">
                                                <span class="px-2 py-1 rounded text-sm
                                                    @if($user->role && $user->role->name == 'Admin') bg-red-900 text-red-200
                                                    @elseif($user->role && str_contains($user->role->name, 'Manager')) bg-blue-900 text-blue-200
                                                    @elseif($user->role && str_contains($user->role->name, 'Employee')) bg-yellow-900 text-yellow-200
                                                    @else bg-green-900 text-green-200 @endif">
                                                    {{ $user->role->name ?? 'No Role' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-right border border-gray-600">
                                                @if($user->accounts && $user->accounts->count() > 0)
                                                    <span class="font-semibold {{ $user->accounts->sum('balance') >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                                        ${{ number_format($user->accounts->sum('balance'), 2) }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">N/A</span>
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
                    @else
                        <p class="text-gray-400">No users found.</p>
                    @endif
                </div>
            </div>

            <!-- Summary Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-8">
                <div class="bg-gray-800 shadow-md rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-200 mb-2">Total Users</h3>
                    <p class="text-3xl font-bold text-indigo-400">{{ $users->count() }}</p>
                </div>
                <div class="bg-gray-800 shadow-md rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-200 mb-2">Clients</h3>
                    <p class="text-3xl font-bold text-green-400">
                        {{ $users->filter(function($user) { return $user->role && in_array($user->role->id, [1,2,3]); })->count() }}
                    </p>
                </div>
                <div class="bg-gray-800 shadow-md rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-200 mb-2">Staff</h3>
                    <p class="text-3xl font-bold text-blue-400">
                        {{ $users->filter(function($user) { return $user->role && in_array($user->role->id, [4,5,6,7,8]); })->count() }}
                    </p>
                </div>
                <div class="bg-gray-800 shadow-md rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-200 mb-2">Total Balance</h3>
                    <p class="text-2xl font-bold text-yellow-400">
                        ${{ number_format($users->sum(function($user) { return $user->accounts ? $user->accounts->sum('balance') : 0; }), 2) }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
