<x-app-layout>
    <div class="p-6">
        <h1 class="text-2xl font-bold">Dashboard PC</h1>
        <p>Login sebagai: {{ auth()->user()->name }}</p>
        <p>Role: {{ auth()->user()->role }}</p>
    </div>
</x-app-layout>