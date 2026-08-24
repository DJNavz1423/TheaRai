@extends('layouts.admin')

@section('title', 'Personal Info')

@section('content')
  <div class="container">
    <div class="row mb-3">
      <h1 class="heading">My Account</h1>
    </div>

    <div class="modal-dialog">
      <form id="accountForm" action="{{ route('my.account.update') }}" method="POST" class="modal-content">
        @csrf
        @method('PUT')

        <div class="modal-header">
          <h2>Personal Information</h2>
        </div>

        <div class="modal-body">
          {{-- Name --}}
           <div class="input-group mb-3">
              <label for="account_name">Full Name</label>

              <input
                  type="text"
                  id="account_name"
                  name="name"
                  value="{{ $user->name }}"
                  required
                  maxlength="255"
              >
          </div>

          {{-- Email --}}
          <div class="input-group mb-3">
            <label for="account_email">Email Address</label>

            <div class="row" style="gap: 0;">
                <input
                    type="text"
                    id="account_email"
                    value="{{ str_replace('@thearai.com.ph', '', $user->email) }}"
                    required
                    style="flex: 1;"
                >

                <span
                    style="
                        display: flex;
                        align-items: center;
                        padding: 0 12px;
                        background: var(--secondary-light);
                        border: 1px solid var(--border);
                        border-left: none;
                        white-space: nowrap;
                    "
                >
                    @thearai.com.ph
                </span>
            </div>

            <input
                type="hidden"
                name="email"
                id="account_full_email"
                value="{{ $user->email }}"
            >
          </div>

          {{-- Phone --}}
          <div class="input-group mb-3">
            <label for="account_phone">Phone Number</label>

            <input
                type="text"
                id="account_phone"
                name="phone_number"
                value="{{ $user->phone_number ?? '' }}"
                maxlength="11"
                minlength="11"
                inputmode="numeric"
                pattern="[0-9]{11}"
                placeholder="Enter your phone number"
                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)"
            >
          </div>

          {{-- Joined Date --}}
          <div class="input-group mb-3">
            <label for="account_joined">Joined Date</label>

            <input
                type="text"
                id="account_joined"
                value="{{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('F j, Y') : '--' }}"
                readonly
                disabled
            >
          </div>
        </div>

        <div class="modal-footer">
          <button
              type="submit"
              class="btn"
              id="updateAccountButton"
              disabled
          >
              Update Account
          </button>
      </div>
      </form>
    </div>
  </div>
@endsection

@once
  @push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/sectionHeading.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tomSelect/tomSelect.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tomSelect/tomSelectCssConfig.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/peopleMng/myAccount.css') }}">
  @endpush
@endonce

@once 
  @push('scripts')
  <script type="text/javascript" src="{{ asset('js/tomSelect/tomSelect.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/tomSelect/tomSelectConfig.js') }}"></script>

  <script>
  document.addEventListener('DOMContentLoaded', function () {
    const nameInput =
        document.getElementById('account_name');

    const emailInput =
        document.getElementById('account_email');

    const fullEmailInput =
        document.getElementById('account_full_email');

    const phoneInput =
        document.getElementById('account_phone');

    const updateButton =
        document.getElementById('updateAccountButton');

    // Original values from database
    const originalName =
        @json($user->name);

    const originalEmail =
        @json($user->email);

    const originalPhone =
        @json($user->phone_number ?? '');

    function updateEmailValue() {

        const emailName =
            emailInput.value.trim();

        fullEmailInput.value =
            emailName + '@thearai.com.ph';
    }

    function checkForChanges() {

        updateEmailValue();

        const currentName =
            nameInput.value.trim();

        const currentEmail =
            fullEmailInput.value.trim().toLowerCase();

        const currentPhone =
            phoneInput.value.trim();

        const changed =
            currentName !== originalName ||
            currentEmail !== originalEmail.toLowerCase() ||
            currentPhone !== originalPhone;

        updateButton.disabled = !changed;
    }

    nameInput.addEventListener(
        'input',
        checkForChanges
    );

    emailInput.addEventListener(
        'input',
        function () {

            // Only allow valid characters for the
            // part before @thearai.com.ph
            this.value =
                this.value.replace(
                    /[^a-zA-Z0-9._%+-]/g,
                    ''
                );

            checkForChanges();
        }
    );

    phoneInput.addEventListener(
        'input',
        function () {

            this.value =
                this.value
                    .replace(/[^0-9]/g, '')
                    .slice(0, 11);

            checkForChanges();
        }
    );

    checkForChanges();
  });
</script>

    @if(session('error'))
      <script>
          alert("🚨 ERROR: {{ session('error') }}");
      </script>
    @endif

    @if(session('success'))
      <script>
          alert("✅ SUCCESS: {{ session('success') }}");
      </script>
    @endif

    @if($errors->any())
    <script>
        let errorMessages =
            "⚠️ Please fix the following errors:\n\n";

        @foreach ($errors->all() as $error)
            errorMessages +=
                "• {{ $error }}\n";
        @endforeach

        alert(errorMessages);
    </script>
    @endif
  @endpush
@endonce