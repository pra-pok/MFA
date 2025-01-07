<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Role') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <form action="{{ route('role.store') }}" method="POST">
            @csrf
            <input type="text" name="name" value="" placeholder="Enter Role">
            @error('name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
            <button type="submit">Create</button>
        </form>
    </div>
</x-app-layout>
