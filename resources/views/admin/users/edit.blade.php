@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Edit User</h1>

    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="max-w-md">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="name" class="block text-sm font-medium mb-2">Name</label>
            <input type="text" name="name" id="name" value="{{ $user->name }}" class="w-full px-4 py-2 border rounded" required>
            @error('name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-4">
            <label for="email" class="block text-sm font-medium mb-2">Email</label>
            <input type="email" name="email" id="email" value="{{ $user->email }}" class="w-full px-4 py-2 border rounded" required>
            @error('email')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-4">
            <label for="is_admin" class="flex items-center">
                <input type="checkbox" name="is_admin" id="is_admin" {{ $user->is_admin ? 'checked' : '' }} class="mr-2">
                <span>Admin</span>
            </label>
        </div>

        <button type="submit" class="bg-amber-600 text-white px-4 py-2 rounded hover:bg-amber-700">Update User</button>
        <a href="{{ route('admin.users.index') }}" class="ml-2 text-gray-600 hover:text-gray-800">Cancel</a>
    </form>
</div>
@endsection
