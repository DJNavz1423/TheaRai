@extends('layouts.offline')

@section('title', 'Select POS Branch')

@section('content')

<div class="container">
    <div class="mb-4">
        <h1 class="heading">Select Active Branch</h1>
        <p class="text-muted">Select the branch you are operating in offline mode.</p>
        <p id="offlineUserInfo" class="text-muted"></p>
    </div>


    <div id="offlineBranchContainer" class="card-container">
        <p class="text-muted">Loading branches...</p>
    </div>

</div>

@endsection

@once
  @push('styles')
  <link rel="stylesheet" href="{{ asset('css/admin/pos/selectBranch.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin/sectionHeading.css') }}">
  @endpush
@endonce

@once
  @push('scripts')
  <script src="{{ asset('js/offline/pos/offlineBranchSelect.js') }}" defer></script>
  @endpush
@endonce