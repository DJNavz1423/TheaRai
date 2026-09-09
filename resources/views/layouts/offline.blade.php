<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- css links --}}
  <link rel="icon" href="{{ asset('img/logos/TheaRaiLogo_Secondary.ico') }}" type="image/x-icon">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/loader.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin/sidebar.css') }}">

  @stack('styles')
  <title>TheaRai Eatery | @yield('title', 'Offline')</title>
</head>

<body>
  @include('partials.loader')

  <aside id="sidebar">
    <nav>
      <ul>
        <li>
          <span class="logo d-flex">
            <img src="{{ asset('img/logos/TheaRaiLogo.webp') }}" alt="TheaRai Logo">
          </span>

          <button onclick=toggleSidebar() id="toggle-btn" class="sidebar-btn">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="m313-480 155 156q11 11 11.5 27.5T468-268q-11 11-28 11t-28-11L228-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 27.5-11.5T468-692q11 11 11 28t-11 28L313-480Zm264 0 155 156q11 11 11.5 27.5T732-268q-11 11-28 11t-28-11L492-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 27.5-11.5T732-692q11 11 11 28t-11 28L577-480Z"/></svg>
          </button>
        </li>

        {{-- Navigation tabs --}}

        <li class="{{ request()->routeIs('offline.pos') ? 'active' : '' }}">
          <a href="/offline-pos">
            <span class="icon-wrapper">
              <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M520-640v-160q0-17 11.5-28.5T560-840h240q17 0 28.5 11.5T840-800v160q0 17-11.5 28.5T800-600H560q-17 0-28.5-11.5T520-640ZM120-480v-320q0-17 11.5-28.5T160-840h240q17 0 28.5 11.5T440-800v320q0 17-11.5 28.5T400-440H160q-17 0-28.5-11.5T120-480Zm400 320v-320q0-17 11.5-28.5T560-520h240q17 0 28.5 11.5T840-480v320q0 17-11.5 28.5T800-120H560q-17 0-28.5-11.5T520-160Zm-400 0v-160q0-17 11.5-28.5T160-360h240q17 0 28.5 11.5T440-320v160q0 17-11.5 28.5T400-120H160q-17 0-28.5-11.5T120-160Zm80-360h160v-240H200v240Zm400 320h160v-240H600v240Zm0-480h160v-80H600v80ZM200-200h160v-80H200v80Zm160-320Zm240-160Zm0 240ZM360-280Z"/></svg>
            </span>

            <span class="nav-item">
                P.O.S
            </span>
          </a>
        </li>

        <li>
          <a href="#">
            <span class="icon-wrapper">
              <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                <path d="M160-160q-33 0-56.5-23.5T80-240v-480q0-33 23.5-56.5T160-800h640q33 0 56.5 23.5T880-720v480q0 33-23.5 56.5T800-160H160Zm0-80h640v-480H160v480Zm80-80h480v-80H240v80Zm0-160h480v-80H240v80Z"/>
              </svg>
            </span>

            <span class="nav-item">
                Transactions
            </span>
          </a>
        </li>

        <li>
          <form action="{{ url('/logout') }}" method="POST">
            @csrf
            <button id="logout-btn" class="sidebar-btn">
              <span class="icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h240q17 0 28.5 11.5T480-800q0 17-11.5 28.5T440-760H200v560h240q17 0 28.5 11.5T480-160q0 17-11.5 28.5T440-120H200Zm487-320H400q-17 0-28.5-11.5T360-480q0-17 11.5-28.5T400-520h287l-75-75q-11-11-11-27t11-28q11-12 28-12.5t29 11.5l143 143q12 12 12 28t-12 28L669-309q-12 12-28.5 11.5T612-310q-11-12-10.5-28.5T613-366l74-74Z"/></svg>
              </span>
              <span class="nav-item">Logout</span>
            </button>
          </form>
        </li>
      </ul>
    </nav>
  </aside>

  <main>
    <section>
    @yield('content')
    </section>
  </main>

  <script type="text/javascript" src="{{ asset('js/offline/DB/offlineDB.js') }}" defer></script>
  <script type="text/javascript" src="{{ asset('js/offline/auth/offlineAuth.js') }}" defer></script>
  <script type="text/javascript" src="{{ asset('js/offline/auth/password.js') }}" defer></script>
  
  <script type="text/javascript" src="{{ asset('js/dashboard/sidebarToggles.js') }}" defer></script>
  <script type="text/javascript" src="{{ asset('js/script.js') }}" defer></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const logoutForm = document
        .getElementById('logout-btn')
        ?.closest('form');

      if (!logoutForm) {
        return;
      }

      logoutForm.addEventListener('submit', function (event) {
        event.preventDefault();
        sessionStorage.removeItem('offline_auth');
        localStorage.removeItem('offline_active_branch_id');
        window.location.href = '/login';
      });
    });
  </script>

  @stack('scripts')

</body>
</html>