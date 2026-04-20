@extends('layouts.admin')

@section('title', 'Select POS Branch')

@section('content')
<div class="container" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 70vh;">
    <div style="text-align: center; margin-bottom: 2rem;">
        <h1 class="heading" style="font-size: 2rem; margin-bottom: 0.5rem;">Select Active Branch</h1>
        <p class="text-muted">You are accessing the POS as an Admin. Which branch are you operating today?</p>
    </div>

    <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; justify-content: center;">
        @foreach($branches as $branch)
            <a href="{{ route('admin.pos.set', $branch->id) }}" style="text-decoration: none; color: inherit;">
                <div style="border: 1px solid var(--secondary-soft); padding: 2rem; border-radius: 12px; background: white; text-align: center; min-width: 250px; transition: 0.2s ease; cursor: pointer;" onmouseover="this.style.borderColor='var(--primary)'; this.style.transform='translateY(-5px)';" onmouseout="this.style.borderColor='var(--secondary-soft)'; this.style.transform='none';">
                    <span class="icon-wrapper" style="margin-bottom: 1rem; display: flex; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="var(--primary)"><path d="M120-120v-560h160v-160h400v320h160v400H120Zm80-80h80v-400h-80v400Zm160 0h240v-640H360v640Zm320 0h80v-240h-80v240ZM360-200Z"/></svg>
                    </span>
                    <h2 style="font-size: 1.5rem; margin-bottom: 0.5rem;">{{ $branch->name }}</h2>
                    <p class="text-muted" style="font-size: 0.9rem;">{{ $branch->address ?? 'No address provided' }}</p>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection