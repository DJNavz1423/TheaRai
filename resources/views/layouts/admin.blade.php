<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- css links -->
  <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('css/loader.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('css/utilities.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('css/dashboard/dashboard.css') }}?v={{ time() }}">
  <title>TheaRai Eatery | Dashboard</title>
</head>

<body>
  <aside id="sidebar">
    <nav>
      <ul class="d-flex">
        <!-- logo -->
        <li>
          <span class="logo d-flex">
            <img src="{{ asset('img/logos/TheaRaiLogo.png') }}" alt="TheaRai Logo">
          </span>

          <button id="toggle-btn">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="m313-480 155 156q11 11 11.5 27.5T468-268q-11 11-28 11t-28-11L228-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 27.5-11.5T468-692q11 11 11 28t-11 28L313-480Zm264 0 155 156q11 11 11.5 27.5T732-268q-11 11-28 11t-28-11L492-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 27.5-11.5T732-692q11 11 11 28t-11 28L577-480Z"/></svg>
          </button>
        </li>

        <!-- Navigation tabs -->


        <li>
          <a href="{{ url('/admin/dashboard') }}" class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
          <span class="icon-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M520-640v-160q0-17 11.5-28.5T560-840h240q17 0 28.5 11.5T840-800v160q0 17-11.5 28.5T800-600H560q-17 0-28.5-11.5T520-640ZM120-480v-320q0-17 11.5-28.5T160-840h240q17 0 28.5 11.5T440-800v320q0 17-11.5 28.5T400-440H160q-17 0-28.5-11.5T120-480Zm400 320v-320q0-17 11.5-28.5T560-520h240q17 0 28.5 11.5T840-480v320q0 17-11.5 28.5T800-120H560q-17 0-28.5-11.5T520-160Zm-400 0v-160q0-17 11.5-28.5T160-360h240q17 0 28.5 11.5T440-320v160q0 17-11.5 28.5T400-120H160q-17 0-28.5-11.5T120-160Zm80-360h160v-240H200v240Zm400 320h160v-240H600v240Zm0-480h160v-80H600v80ZM200-200h160v-80H200v80Zm160-320Zm240-160Zm0 240ZM360-280Z"/></svg>
          </span>
          <span>Dashboard</span>
          </a>
        </li>

        <!-- dropdown li -->
        <li>
          <button class="dropdown-btn">
          <span class="icon-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="m620-275 198-198q11-11 28-11t28 11q11 11 11 28t-11 28L648-191q-12 12-28 12t-28-12L478-305q-11-11-11-28t11-28q11-11 28-11t28 11l86 86ZM200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h167q11-35 43-57.5t70-22.5q40 0 71.5 22.5T594-840h166q33 0 56.5 23.5T840-760v160q0 17-11.5 28.5T800-560q-17 0-28.5-11.5T760-600v-160h-80v80q0 17-11.5 28.5T640-640H320q-17 0-28.5-11.5T280-680v-80h-80v560h200q17 0 28.5 11.5T440-160q0 17-11.5 28.5T400-120H200Zm308.5-651.5Q520-783 520-800t-11.5-28.5Q497-840 480-840t-28.5 11.5Q440-817 440-800t11.5 28.5Q463-760 480-760t28.5-11.5Z"/></svg>
          </span>

          <span>Inventory</span>

          <span class="icon-wrapper">
              <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M465-363.5q-7-2.5-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5q-8 0-15-2.5Z"/></svg>
            </span>
          </button>

          <ul class="sub-menu">
            <li><a href="{{ url('admin/inventory/inventory') }}">Stock List</a></li>
            <li><a href="#">Transactions</a></li>
            <li><a href="#">QR Orders</a></li>
            <li><a href="#">Analytics</a></li>
          </ul>
        </li>
        <!-- end of dropdown -->

        <!-- dropdown li -->
        <li>
          <button class="dropdown-btn">
            <span class="icon-wrapper">
              <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M856-390 570-104q-12 12-27 18t-30 6q-15 0-30-6t-27-18L103-457q-11-11-17-25.5T80-513v-287q0-33 23.5-56.5T160-880h287q16 0 31 6.5t26 17.5l352 353q12 12 17.5 27t5.5 30q0 15-5.5 29.5T856-390ZM513-160l286-286-353-354H160v286l353 354ZM260-640q25 0 42.5-17.5T320-700q0-25-17.5-42.5T260-760q-25 0-42.5 17.5T200-700q0 25 17.5 42.5T260-640Zm220 160Z"/></svg>
            </span>

            <span>
              Sales
            </span>

            <span class="icon-wrapper">
              <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M465-363.5q-7-2.5-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5q-8 0-15-2.5Z"/></svg>
            </span>
          </button>

          <ul class="sub-menu">
            <li><a href="#">Overview</a></li>
            <li><a href="#">Transactions</a></li>
            <li><a href="#">QR Orders</a></li>
            <li><a href="#">Analytics</a></li>
          </ul>
        </li>
          <!-- end of dropdown -->
        <li>
          <form action="{{ url('/logout') }}" method="POST">
            @csrf
            <button>
              <span class="icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h240q17 0 28.5 11.5T480-800q0 17-11.5 28.5T440-760H200v560h240q17 0 28.5 11.5T480-160q0 17-11.5 28.5T440-120H200Zm487-320H400q-17 0-28.5-11.5T360-480q0-17 11.5-28.5T400-520h287l-75-75q-11-11-11-27t11-28q11-12 28-12.5t29 11.5l143 143q12 12 12 28t-12 28L669-309q-12 12-28.5 11.5T612-310q-11-12-10.5-28.5T613-366l74-74Z"/></svg>
              </span>
              <span>Logout</span>
            </button>
          </form>
        </li>
      </ul>
    </nav>
  </aside>

  <main>
    @yield('content')
  </main>

  @include('loader')

</body>
</html>