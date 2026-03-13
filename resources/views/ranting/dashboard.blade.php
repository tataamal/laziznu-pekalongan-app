<x-app-layout>
    <div class="p-6">
        <h1 class="text-2xl font-bold">Dashboard Ranting</h1>
        <p>Login sebagai: {{ auth()->user()->name }}</p>
        <p>Role: {{ auth()->user()->role }}</p>
        <p>Wilayah: {{ auth()->user()->wilayah->nama_wilayah ?? '-' }}</p>
    </div>
</x-app-layout>