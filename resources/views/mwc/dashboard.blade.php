@extends('layouts.app')

@section('page_title', 'Dashboard MWC')
@section('page_subtitle', 'Kelola sistem dengan tampilan yang rapi dan efisien.')

@section('content')
    <div class="p-6">
        <h1 class="text-2xl font-bold">Dashboard MWC</h1>
        <p>Login sebagai: {{ auth()->user()->name }}</p>
        <p>Role: {{ auth()->user()->role }}</p>
        <p>Wilayah: {{ auth()->user()->wilayah->nama_wilayah ?? '-' }}</p>
    </div>
@endsection