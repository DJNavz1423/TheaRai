@extends('layouts.admin')

@section('title', 'Select POS Branch')

@section('content')
<div class="container">
    <div class="mb-4">
        <h1 class="heading">Select Active Branch</h1>
        <p class="text-muted">You are accessing the POS as an Admin. Which branch are you operating today?</p>
    </div>

    <div class="card-container">
        @foreach($branches as $branch)
            <a href="{{ route('admin.pos.set', $branch->id) }}">
                <div class="branch-card">
                    <span class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M200-800h560q17 0 28.5 11.5T800-760q0 17-11.5 28.5T760-720H200q-17 0-28.5-11.5T160-760q0-17 11.5-28.5T200-800Zm0 640q-17 0-28.5-11.5T160-200v-200h-7q-19 0-31-14.5t-8-33.5l40-200q3-14 14-23t25-9h574q14 0 25 9t14 23l40 200q4 19-8 33.5T807-400h-7v200q0 17-11.5 28.5T760-160q-17 0-28.5-11.5T720-200v-200H560v200q0 17-11.5 28.5T520-160H200Zm40-80h240v-160H240v160Z"/></svg>
                    </span>
                    <h2>{{ $branch->name }}</h2>
                    <p class="text-muted">{{ $branch->address ?? 'No address provided' }}</p>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection

@once
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/pos/selectBranch.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/sectionHeading.css') }}">
    @endpush
@endonce