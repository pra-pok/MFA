<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Role') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <form action="{{ route('role.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="text" name="name" value="{{ $role->name }}">
            @error('name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
            <button type="submit">Update</button>
        </form>
    </div>
</x-app-layout>
