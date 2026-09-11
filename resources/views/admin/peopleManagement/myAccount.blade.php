@extends($layout)

@section('title', 'Personal Info')

@section('content')
  <div class="container">
    <div class="row mb-3">
      <h1 class="heading">My Account</h1>
    </div>

    <div class="modal-dialog">
      <form id="accountForm" action="{{ route('settings.myAccount.update') }}" method="POST" class="modal-content" autocomplete="off">
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

                <input
                    type="email"
                    id="account_email"
                    name="email"
                    value="{{ $user->email }}"
                    required
                    maxlength="255"
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
  <script type="text/javascript" src="{{ asset('js/tomSelect/tomSelect.js') }}" defer></script>
  <script type="text/javascript" src="{{ asset('js/tomSelect/tomSelectConfig.js') }}" defer></script>
  <script type="text/javascript" src="{{ asset('js/script.js') }}" defer></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {

        const form =
            document.getElementById('accountForm');

        const nameInput =
            document.getElementById('account_name');

        const emailInput =
            document.getElementById('account_email');

        const phoneInput =
            document.getElementById('account_phone');

        const joinedInput =
            document.getElementById('account_joined');

        const updateButton =
            document.getElementById('updateAccountButton');

        const originalName =
            @json($user->name);

        const originalEmail =
            @json($user->email);

        const originalPhone =
            @json($user->phone_number ?? '');

        /*
        * Keep the original joined date in the browser only for display.
        * It is NOT submitted to the server.
        */
        const originalJoinedDate =
            @json(
                $user->created_at
                    ? \Carbon\Carbon::parse($user->created_at)->format('F j, Y')
                    : '--'
            );

        function enforceEmailDomain() {

            const domain = '@thearai.com.ph';

            let value = emailInput.value;

            /*
            * Remove anything after the first @.
            * This means the user can never change the domain.
            */
            const atPosition = value.indexOf('@');

            if (atPosition !== -1) {
                value = value.substring(0, atPosition);
            }

            /*
            * Only allow characters valid before the @.
            */
            value = value.replace(
                /[^a-zA-Z0-9._%+-]/g,
                ''
            );

            emailInput.value =
                value + domain;
        }

        function checkForChanges() {

            enforceEmailDomain();

            const currentName =
                nameInput.value.trim();

            const currentEmail =
                emailInput.value.trim().toLowerCase();

            const currentPhone =
                phoneInput.value.trim();

            const changed =
                currentName !== originalName ||
                currentEmail !== originalEmail.toLowerCase() ||
                currentPhone !== originalPhone;

            updateButton.disabled =
                !changed;
        }

        /*
        * Name
        */
        nameInput.addEventListener(
            'input',
            checkForChanges
        );

        /*
        * Email
        */
        emailInput.addEventListener(
            'input',
            function () {
                enforceEmailDomain();
                checkForChanges();
            }
        );

        emailInput.addEventListener(
            'keydown',
            function (event) {

                const domain =
                    '@thearai.com.ph';

                const cursorPosition =
                    this.selectionStart;

                /*
                * Prevent Backspace/Delete from removing
                * the domain.
                */
                if (
                    cursorPosition > this.value.indexOf('@') ||
                    (
                        cursorPosition === this.value.indexOf('@') &&
                        (
                            event.key === 'Delete' ||
                            event.key === 'Backspace'
                        )
                    )
                ) {
                    event.preventDefault();

                    /*
                    * Put cursor before the @.
                    */
                    const atPosition =
                        this.value.indexOf('@');

                    if (atPosition !== -1) {
                        this.setSelectionRange(
                            atPosition,
                            atPosition
                        );
                    }

                    return;
                }

                /*
                * Prevent typing another @.
                */
                if (event.key === '@') {
                    event.preventDefault();
                }
            }
        );

        /*
        * Phone
        */
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

        /*
        * Joined date:
        * Always restore the original display value.
        *
        * More importantly, it is NOT named "created_at",
        * therefore it is never submitted to the controller.
        */
        joinedInput.addEventListener(
            'input',
            function () {
                this.value =
                    originalJoinedDate;
            }
        );

        /*
        * Extra protection:
        * Before submitting, force the joined-date display back
        * to its original value.
        */
        form.addEventListener(
            'submit',
            function () {
                joinedInput.value =
                    originalJoinedDate;
            }
        );

        checkForChanges();
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

    preventEmptyInput(
        document.getElementById('account_name')
    );

    preventEmptyInput(
        document.getElementById('account_email'),
        true
    );

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