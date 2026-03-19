<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- css links -->
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/loader.css') }}">
  <link rel="stylesheet" href="{{ asset('css/dashboard/sidebar.css') }}">

  @stack('styles')

  <title>TheaRai Eatery | Dashboard</title>
</head>

<body>
  @include('loader')

  <aside id="sidebar">
    <nav>
      <ul>
        <!-- logo -->
        <li>
          <span class="logo d-flex">
            <img src="{{ asset('img/logos/TheaRaiLogo.png') }}" alt="TheaRai Logo">
          </span>

          <button onclick=toggleSidebar() id="toggle-btn" class="sidebar-btn">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="m313-480 155 156q11 11 11.5 27.5T468-268q-11 11-28 11t-28-11L228-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 27.5-11.5T468-692q11 11 11 28t-11 28L313-480Zm264 0 155 156q11 11 11.5 27.5T732-268q-11 11-28 11t-28-11L492-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 27.5-11.5T732-692q11 11 11 28t-11 28L577-480Z"/></svg>
          </button>
        </li>

        <!-- Navigation tabs -->


        <li class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
          <a href="{{ url('/admin/dashboard') }}">
          <span class="icon-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M520-640v-160q0-17 11.5-28.5T560-840h240q17 0 28.5 11.5T840-800v160q0 17-11.5 28.5T800-600H560q-17 0-28.5-11.5T520-640ZM120-480v-320q0-17 11.5-28.5T160-840h240q17 0 28.5 11.5T440-800v320q0 17-11.5 28.5T400-440H160q-17 0-28.5-11.5T120-480Zm400 320v-320q0-17 11.5-28.5T560-520h240q17 0 28.5 11.5T840-480v320q0 17-11.5 28.5T800-120H560q-17 0-28.5-11.5T520-160Zm-400 0v-160q0-17 11.5-28.5T160-360h240q17 0 28.5 11.5T440-320v160q0 17-11.5 28.5T400-120H160q-17 0-28.5-11.5T120-160Zm80-360h160v-240H200v240Zm400 320h160v-240H600v240Zm0-480h160v-80H600v80ZM200-200h160v-80H200v80Zm160-320Zm240-160Zm0 240ZM360-280Z"/></svg>
          </span>
          <span class="nav-item">Dashboard</span>
          </a>
        </li>

        <!-- dropdown inventory -->
        <li>
          <button onclick=toggleSubMenu(this) class="dropdown-btn sidebar-btn">
          <span class="icon-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="m620-275 198-198q11-11 28-11t28 11q11 11 11 28t-11 28L648-191q-12 12-28 12t-28-12L478-305q-11-11-11-28t11-28q11-11 28-11t28 11l86 86ZM200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h167q11-35 43-57.5t70-22.5q40 0 71.5 22.5T594-840h166q33 0 56.5 23.5T840-760v160q0 17-11.5 28.5T800-560q-17 0-28.5-11.5T760-600v-160h-80v80q0 17-11.5 28.5T640-640H320q-17 0-28.5-11.5T280-680v-80h-80v560h200q17 0 28.5 11.5T440-160q0 17-11.5 28.5T400-120H200Zm308.5-651.5Q520-783 520-800t-11.5-28.5Q497-840 480-840t-28.5 11.5Q440-817 440-800t11.5 28.5Q463-760 480-760t28.5-11.5Z"/></svg>
          </span>

          <span class="nav-item">Inventory</span>

          <span class="icon-wrapper dropdown-arrow">
              <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M465-363.5q-7-2.5-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5q-8 0-15-2.5Z"/></svg>
            </span>
          </button>

          <ul class="sub-menu">
            <div>
              <li class="{{ request()->is('admin/inventory') ? 'active' : '' }}"><a href="{{ url('admin/inventory/inventory') }}">Stock List</a></li>
              <li><a href="#">Categories</a></li>
              <li><a href="#">Units of Measure</a></li>
            </div>
          </ul>
        </li>
        <!-- end of inventory -->

        <!-- dropdown sales -->
        <li>
          <button onclick=toggleSubMenu(this) class="dropdown-btn sidebar-btn">
            <span class="icon-wrapper">
              <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M856-390 570-104q-12 12-27 18t-30 6q-15 0-30-6t-27-18L103-457q-11-11-17-25.5T80-513v-287q0-33 23.5-56.5T160-880h287q16 0 31 6.5t26 17.5l352 353q12 12 17.5 27t5.5 30q0 15-5.5 29.5T856-390ZM513-160l286-286-353-354H160v286l353 354ZM260-640q25 0 42.5-17.5T320-700q0-25-17.5-42.5T260-760q-25 0-42.5 17.5T200-700q0 25 17.5 42.5T260-640Zm220 160Z"/></svg>
            </span>

            <span class="nav-item">Sales</span>

            <span class="icon-wrapper dropdown-arrow">
              <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M465-363.5q-7-2.5-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5q-8 0-15-2.5Z"/></svg>
            </span>
          </button>

          <ul class="sub-menu">
            <div>
              <li><a href="#">Overview</a></li>
              <li><a href="#">Transactions</a></li>
              <li><a href="#">QR Orders</a></li>
            </div>
          </ul>
        </li>
          <!-- end of sales -->

        <li>
          <a href="">
            <span class="icon-wrapper">
              <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M200-120q-33 0-56.5-23.5T120-200v-600q0-17 11.5-28.5T160-840q17 0 28.5 11.5T200-800v600h600q17 0 28.5 11.5T840-160q0 17-11.5 28.5T800-120H200Zm80-120q-17 0-28.5-11.5T240-280v-280q0-17 11.5-28.5T280-600h80q17 0 28.5 11.5T400-560v280q0 17-11.5 28.5T360-240h-80Zm200 0q-17 0-28.5-11.5T440-280v-480q0-17 11.5-28.5T480-800h80q17 0 28.5 11.5T600-760v480q0 17-11.5 28.5T560-240h-80Zm200 0q-17 0-28.5-11.5T640-280v-120q0-17 11.5-28.5T680-440h80q17 0 28.5 11.5T800-400v120q0 17-11.5 28.5T760-240h-80Z"/></svg>
            </span>

            <span class="nav-item">Analytics Report</span>
          </a>
        </li>

        <!-- dropdown menu -->
          <li>
            <button onclick=toggleSubMenu(this) class="dropdown-btn sidebar-btn">
              <span class="icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M240-80q-33 0-56.5-23.5T160-160v-80q-17 0-28.5-11.5T120-280q0-17 11.5-28.5T160-320v-120q-17 0-28.5-11.5T120-480q0-17 11.5-28.5T160-520v-120q-17 0-28.5-11.5T120-680q0-17 11.5-28.5T160-720v-80q0-33 23.5-56.5T240-880h480q33 0 56.5 23.5T800-800v640q0 33-23.5 56.5T720-80H240Zm0-80h480v-640H240v80q17 0 28.5 11.5T280-680q0 17-11.5 28.5T240-640v120q17 0 28.5 11.5T280-480q0 17-11.5 28.5T240-440v120q17 0 28.5 11.5T280-280q0 17-11.5 28.5T240-240v80Zm140-280v130q0 13 8.5 21.5T410-280q13 0 21.5-8.5T440-310v-130q26-7 43-28.5t17-48.5v-143q0-8-6-14t-14-6q-8 0-14 6t-6 14v131h-30v-131q0-8-6-14t-14-6q-8 0-14 6t-6 14v131h-30v-131q0-8-6-14t-14-6q-8 0-14 6t-6 14v143q0 27 17 48.5t43 28.5Zm220 0v130q0 13 8.5 21.5T630-280q13 0 21.5-8.5T660-310v-347q0-11-7.5-17t-19.5-6q-13 0-28.5 7T575-652q-17 17-26 38.5t-9 46.5v87q0 17 11.5 28.5T580-440h20ZM240-160v-640 640Z"/></svg>
              </span>
              
              <span class="nav-item">Manage Menu</span>
              
              <span class="icon-wrapper dropdown-arrow">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M465-363.5q-7-2.5-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5q-8 0-15-2.5Z"/></svg>
              </span>
            </button>

            <ul class="sub-menu">
              <div>
                <li><a href="">Menu Items</a></li>
                <li><a href="">Menu Categories</a></li>
                <li><a href="">Quick POS</a></li>
              </div>
            </ul>
          </li>
        <!-- dropdown menu end-->

        <!-- dropdown people management -->
        <li>
          <button onclick=toggleSubMenu(this) class="dropdown-btn sidebar-btn">
            <span class="icon-wrapper">
              <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M40-272q0-34 17.5-62.5T104-378q62-31 126-46.5T360-440q66 0 130 15.5T616-378q29 15 46.5 43.5T680-272v32q0 33-23.5 56.5T600-160H120q-33 0-56.5-23.5T40-240v-32Zm800 112H738q11-18 16.5-38.5T760-240v-40q0-44-24.5-84.5T666-434q51 6 96 20.5t84 35.5q36 20 55 44.5t19 53.5v40q0 33-23.5 56.5T840-160ZM247-527q-47-47-47-113t47-113q47-47 113-47t113 47q47 47 47 113t-47 113q-47 47-113 47t-113-47Zm466 0q-47 47-113 47-11 0-28-2.5t-28-5.5q27-32 41.5-71t14.5-81q0-42-14.5-81T544-792q14-5 28-6.5t28-1.5q66 0 113 47t47 113q0 66-47 113ZM120-240h480v-32q0-11-5.5-20T580-306q-54-27-109-40.5T360-360q-56 0-111 13.5T140-306q-9 5-14.5 14t-5.5 20v32Zm296.5-343.5Q440-607 440-640t-23.5-56.5Q393-720 360-720t-56.5 23.5Q280-673 280-640t23.5 56.5Q327-560 360-560t56.5-23.5ZM360-240Zm0-400Z"/></svg>
            </span>
            
            <span class="nav-item">Manage People</span>
            
            <span class="icon-wrapper dropdown-arrow">
              <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M465-363.5q-7-2.5-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5q-8 0-15-2.5Z"/></svg>
            </span>
          </button>

          <ul class="sub-menu">
            <div>
              <li><a href="#">Manage Staffs</a></li>
              <li><a href="#">User Accounts</a></li>
              <li><a href="#">Contacts</a></li>
            </div>
          </ul>
        </li>
        <!-- dropdown people management end -->

          <!-- dropdown settings -->
        <li>
           <button onclick=toggleSubMenu(this) class="dropdown-btn sidebar-btn">
            <span class="icon-wrapper">
              <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M433-80q-27 0-46.5-18T363-142l-9-66q-13-5-24.5-12T307-235l-62 26q-25 11-50 2t-39-32l-47-82q-14-23-8-49t27-43l53-40q-1-7-1-13.5v-27q0-6.5 1-13.5l-53-40q-21-17-27-43t8-49l47-82q14-23 39-32t50 2l62 26q11-8 23-15t24-12l9-66q4-26 23.5-44t46.5-18h94q27 0 46.5 18t23.5 44l9 66q13 5 24.5 12t22.5 15l62-26q25-11 50-2t39 32l47 82q14 23 8 49t-27 43l-53 40q1 7 1 13.5v27q0 6.5-2 13.5l53 40q21 17 27 43t-8 49l-48 82q-14 23-39 32t-50-2l-60-26q-11 8-23 15t-24 12l-9 66q-4 26-23.5 44T527-80h-94Zm7-80h79l14-106q31-8 57.5-23.5T639-327l99 41 39-68-86-65q5-14 7-29.5t2-31.5q0-16-2-31.5t-7-29.5l86-65-39-68-99 42q-22-23-48.5-38.5T533-694l-13-106h-79l-14 106q-31 8-57.5 23.5T321-633l-99-41-39 68 86 64q-5 15-7 30t-2 32q0 16 2 31t7 30l-86 65 39 68 99-42q22 23 48.5 38.5T427-266l13 106Zm42-180q58 0 99-41t41-99q0-58-41-99t-99-41q-59 0-99.5 41T342-480q0 58 40.5 99t99.5 41Zm-2-140Z"/></svg>
            </span>

            <span class="nav-item">Settings</span>

            <span class="icon-wrapper dropdown-arrow">
              <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M465-363.5q-7-2.5-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5q-8 0-15-2.5Z"/></svg>
            </span>
          </button>

          <ul class="sub-menu">
            <div>
              <li><a href="#">General</a></li>
              <li><a href="#">My Account</a></li>
              <li><a href="#">Feature Settings</a></li>
            </div>
          </ul>
        </li>
          <!-- end of settings -->

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
    @yield('content')
  </main>

  <script type="text/javascript" src="{{ asset('js/dashboard/sidebarToggles.js') }}" defer></script>

  @stack('scripts')
</body>
</html>