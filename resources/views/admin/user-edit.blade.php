<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Edit User - {{ $userToEdit->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Back Link -->
            <div class="mb-4">
                <a href="{{ route('admin.dashboard') }}" class="text-indigo-400 hover:text-indigo-200">
                    ← Back to Dashboard
                </a>
            </div>

            <!-- Edit User Form -->
            <div class="bg-gray-800 shadow-md rounded-lg p-6">
                <form method="POST" action="{{ route('admin.user.update', $userToEdit->id) }}">
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-300">Name</label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name', $userToEdit->name) }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-700 bg-gray-700 text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-300">Email</label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email', $userToEdit->email) }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-700 bg-gray-700 text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password (Optional) -->
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-300">New Password (leave blank to keep current)</label>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="mt-1 block w-full rounded-md border-gray-700 bg-gray-700 text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Confirmation -->
                    <div class="mb-4">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-300">Confirm New Password</label>
                        <input type="password" 
                               name="password_confirmation" 
                               id="password_confirmation" 
                               class="mt-1 block w-full rounded-md border-gray-700 bg-gray-700 text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <!-- Role -->
                    <div class="mb-4">
                        <label for="role_id" class="block text-sm font-medium text-gray-300">Role</label>
                        <select name="role_id" 
                                id="role_id" 
                                required
                                class="mt-1 block w-full rounded-md border-gray-700 bg-gray-700 text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" 
                                        {{ old('role_id', $userToEdit->role_id) == $role->id ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center gap-4">
                        <button type="submit" 
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Update User
                        </button>
                        <a href="{{ route('admin.dashboard') }}" 
                           class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
