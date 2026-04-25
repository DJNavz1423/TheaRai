 <div class="loader-wrapper">
        <div class="loader"></div>
    </div>

    @once
        @push('scripts')
            <script type="text/javascript" src="{{ asset('js/loader.js') }}"></script>
        @endpush
    @endonce