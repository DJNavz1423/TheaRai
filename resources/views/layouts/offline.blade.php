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
              <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M280-640q-33 0-56.5-23.5T200-720v-80q0-33 23.5-56.5T280-880h400q33 0 56.5 23.5T760-800v80q0 33-23.5 56.5T680-640H280Zm40-80h320q16 0 22.5-14.5T680-760q0-17-11.5-28.5T640-800H320q-17 0-28.5 11.5T280-760q11 11 17.5 25.5T320-720ZM160-80q-33 0-56.5-23.5T80-160v-40h800v40q0 33-23.5 56.5T800-80H160ZM80-240l139-313q10-22 30-34.5t43-12.5h376q23 0 43 12.5t30 34.5l139 313H80Zm260-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm0-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm0-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm120 160h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm0-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm0-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm120 160h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm0-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm0-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Z"/></svg>
            </span>

            <span class="nav-item">
                P.O.S
            </span>
          </a>
        </li>

        <li class="{{ request()->routeIs('offline.transactions') ? 'active' : '' }}">
          <a href="/offline-transactions">
            <span class="icon-wrapper">
              <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M120-128v-728q0-7 6-9.5t11 2.5l29 29q6 6 14 6t14-6l32-32q6-6 14-6t14 6l32 32q6 6 14 6t14-6l32-32q6-6 14-6t14 6l32 32q6 6 14 6t14-6l32-32q6-6 14-6t14 6l32 32q6 6 14 6t14-6l32-32q6-6 14-6t14 6l32 32q6 6 14 6t14-6l32-32q6-6 14-6t14 6l32 32q6 6 14 6t14-6l29-29q5-5 11-2.5t6 9.5v728q0 14-12 19t-22-5l-12-12q-6-6-14-6t-14 6l-32 32q-6 6-14 6t-14-6l-32-32q-6-6-14-6t-14 6l-32 32q-6 6-14 6t-14-6l-32-32q-6-6-14-6t-14 6l-32 32q-6 6-14 6t-14-6l-32-32q-6-6-14-6t-14 6l-32 32q-6 6-14 6t-14-6l-32-32q-6-6-14-6t-14 6l-32 32q-6 6-14 6t-14-6l-32-32q-6-6-14-6t-14 6l-12 12q-10 10-22 5t-12-19Zm160-152h400q17 0 28.5-11.5T720-320q0-17-11.5-28.5T680-360H280q-17 0-28.5 11.5T240-320q0 17 11.5 28.5T280-280Zm0-160h400q17 0 28.5-11.5T720-480q0-17-11.5-28.5T680-520H280q-17 0-28.5 11.5T240-480q0 17 11.5 28.5T280-440Zm0-160h400q17 0 28.5-11.5T720-640q0-17-11.5-28.5T680-680H280q-17 0-28.5 11.5T240-640q0 17 11.5 28.5T280-600Z"/></svg>
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