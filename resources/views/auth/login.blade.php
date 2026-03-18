<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- css links -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/utilities.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/login/login-style.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/loader.css') }}?v={{ time() }}">
    <title>TheaRai Eatery | Login Page</title>
</head>
<!-- MARCH 9 -->
<body>
    <div class="container">
        <div class="row">
            <div class="col">
                <h1 class="login-heading">
                    <span class = "logo d-none">
                        <img src="{{ asset('img/logos/TheaRaiLogo.png') }}" alt="TheaRai Logo">
                    </span>
                    <span class="heading underline-fullwidth d-flex">Admin Login</span>
                </h1>

                <form action="/login" method="POST" class="mt-5">
                     @csrf
                        <!-- email input -->
                        <div class="input-group">
                            <input type="email" name = "email" id="email" value="{{ old('email') }}" class="{{ $errors->has('email') ? 'is-invalid' : '' }}" placeholder="username@thearai.com.ph" required {{ !$errors->has('password') ? 'autofocus' : '' }}>

                            @error('email')
                        <div class="error-message d-flex">
                            <span class="icon-wrapper">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M508.5-291.5Q520-303 520-320t-11.5-28.5Q497-360 480-360t-28.5 11.5Q440-337 440-320t11.5 28.5Q463-280 480-280t28.5-11.5Zm0-160Q520-463 520-480v-160q0-17-11.5-28.5T480-680q-17 0-28.5 11.5T440-640v160q0 17 11.5 28.5T480-440q17 0 28.5-11.5ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>
                            </span>
                            <p class="text-danger">{{ $message }}</p>
                        </div>
                        @enderror
                        </div>

                        <!-- password input -->
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="{{ $errors->has('password') ? 'is-invalid' : '' }}" required placeholder="examplepassword123" {{ $errors->has('password') ? 'autofocus' : '' }}>
                            <button type="button" class="toggle-visibility" aria-label="Show password" id="toggle-btn">
                                <span class="icon-wrapper" id="visibility-on">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M607.5-372.5Q660-425 660-500t-52.5-127.5Q555-680 480-680t-127.5 52.5Q300-575 300-500t52.5 127.5Q405-320 480-320t127.5-52.5Zm-204-51Q372-455 372-500t31.5-76.5Q435-608 480-608t76.5 31.5Q588-545 588-500t-31.5 76.5Q525-392 480-392t-76.5-31.5ZM235.5-272Q125-344 61-462q-5-9-7.5-18.5T51-500q0-10 2.5-19.5T61-538q64-118 174.5-190T480-800q134 0 244.5 72T899-538q5 9 7.5 18.5T909-500q0 10-2.5 19.5T899-462q-64 118-174.5 190T480-200q-134 0-244.5-72ZM480-500Zm207.5 160.5Q782-399 832-500q-50-101-144.5-160.5T480-720q-113 0-207.5 59.5T128-500q50 101 144.5 160.5T480-280q113 0 207.5-59.5Z"/></svg>
                                </span>
                                
                                <span class="icon-wrapper d-none" id="visibility-off">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M607-627q29 29 42.5 66t9.5 76q0 15-11 25.5T622-449q-15 0-25.5-10.5T586-485q5-26-3-50t-25-41q-17-17-41-26t-51-4q-15 0-25.5-11T430-643q0-15 10.5-25.5T466-679q38-4 75 9.5t66 42.5Zm-127-93q-19 0-37 1.5t-36 5.5q-17 3-30.5-5T358-742q-5-16 3.5-31t24.5-18q23-5 46.5-7t47.5-2q137 0 250.5 72T904-534q4 8 6 16.5t2 17.5q0 9-1.5 17.5T905-466q-18 40-44.5 75T802-327q-12 11-28 9t-26-16q-10-14-8.5-30.5T753-392q24-23 44-50t35-58q-50-101-144.5-160.5T480-720Zm0 520q-134 0-245-72.5T60-463q-5-8-7.5-17.5T50-500q0-10 2-19t7-18q20-40 46.5-76.5T166-680l-83-84q-11-12-10.5-28.5T84-820q11-11 28-11t28 11l680 680q11 11 11.5 27.5T820-84q-11 11-28 11t-28-11L624-222q-35 11-71 16.5t-73 5.5ZM222-624q-29 26-53 57t-41 67q50 101 144.5 160.5T480-280q20 0 39-2.5t39-5.5l-36-38q-11 3-21 4.5t-21 1.5q-75 0-127.5-52.5T300-500q0-11 1.5-21t4.5-21l-84-82Zm319 93Zm-151 75Z"/></svg>
                                </span>
                            </button>

                             @error('password')
                        <div class="error-message d-flex">
                            <span class="icon-wrapper">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M508.5-291.5Q520-303 520-320t-11.5-28.5Q497-360 480-360t-28.5 11.5Q440-337 440-320t11.5 28.5Q463-280 480-280t28.5-11.5Zm0-160Q520-463 520-480v-160q0-17-11.5-28.5T480-680q-17 0-28.5 11.5T440-640v160q0 17 11.5 28.5T480-440q17 0 28.5-11.5ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>
                            </span>
                            <p class="text-danger">{{ $message }}</p>
                        </div>
                        @enderror
                        </div>

                        <div class="input-group remember-group">
                        <input type="checkbox" name="remember" id="remember">
                        <label for="remember">Remember me</label>
                        </div>

                        <button class="btn" type="submit" id="btn-login">Login</button>
                </form>
                    <p class="text-muted mt-3">&copy; 2026 TheaRai Eatery | All Rights Reserved</p>
            </div>

            <div class="col">
                 <img src="{{ asset('img/logos/TheaRai_fullLogo.png') }}" alt="TheaRai Logo">
            </div>
        </div>
    </div>

    
    @include('loader')


    <script type="text/javascript" src="{{ asset('js/login/visibility-toggle.js') }}?v={{ time() }}" defer></script>
    <script type="text/javascript" src="{{ asset('js/login/remember-user.js') }}?v={{ time() }}" defer></script>
</body>
</html>
