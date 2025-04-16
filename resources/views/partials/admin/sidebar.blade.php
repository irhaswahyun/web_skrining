<aside class="sidebar-left border-right bg-white shadow" id="leftSidebar" data-simplebar>
    <a href="#" class="btn collapseSidebar toggle-btn d-lg-none text-muted ml-2 mt-3" data-toggle="toggle">
      <i class="fe fe-x"><span class="sr-only"></span></i>
    </a>
    <nav class="vertnav navbar navbar-light">
      <!-- nav bar -->
      <div class="w-100 mb-4 d-flex">
        <a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="./index.html">
          <svg version="1.1" id="logo" class="navbar-brand-img brand-sm" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 120 120" xml:space="preserve">
            <g>
              <polygon class="st0" points="78,105 15,105 24,87 87,87 	" />
              <polygon class="st0" points="96,69 33,69 42,51 105,51 	" />
              <polygon class="st0" points="78,33 15,33 24,15 87,15 	" />
            </g>
          </svg>
        </a>
      </div>
      <ul class="navbar-nav flex-fill w-100 mb-2">
          <a href="{{ route('admin.dashboard') }}" data-toggle="collapse" aria-expanded="false" class="nav-link">
            <i class="fe fe-home fe-16"></i>
            <span class="ml-3 item-text">Dashboard</span>
          </a>
      </ul>
      <ul class="navbar-nav flex-fill w-100 mb-2">
          <a href="admin.manajemen_pengguna.index" data-toggle="collapse" aria-expanded="false" class="nav-link">
            <i class="fe fe-box fe-16"></i>
            <span class="ml-3 item-text">Manajemen Pengguna</span>
          </a>
      </ul>
      <ul class="navbar-nav flex-fill w-100 mb-2">
          <a class="nav-link" href="admin.manajemen_pasien.index">
            <i class="fe fe-layers fe-16"></i>
            <span class="ml-3 item-text">Manajemen Pasien</span>
          </a>
      </ul>
      <ul class="navbar-nav flex-fill w-100 mb-2">
        <li class="nav-item dropdown">
          <a href="#forms" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link">
            <i class="fe fe-credit-card fe-16" ></i>
            <span class="ml-3 item-text">Manajemen <br>Forms Skrining</span>
          </a>
          <ul class="collapse list-unstyled pl-4 w-100" id="forms">
            <li class="nav-item">
              <a class="nav-link pl-3" href="./form_elements.html"><span class="ml-1 item-text">Daftar Penyakit</span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link pl-3" href="./form_advanced.html"><span class="ml-1 item-text">Daftar Pertanyaan</span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link pl-3" href="./form_validation.html"><span class="ml-1 item-text">Form Skrining</span></a>
            </li>
          </ul>
        </li>
      </ul>
      <ul class="navbar-nav flex-fill w-100 mb-2">
        <a href="#tables" data-toggle="collapse" aria-expanded="false" class="nav-link">
          <i class="fe fe-grid fe-16"></i>
          <span class="ml-3 item-text">Skrining</span>
        </a>
      </ul>
      <ul class="navbar-nav flex-fill w-100 mb-2">
        <a href="#charts" data-toggle="collapse" aria-expanded="false" class="nav-link">
          <i class="fe fe-pie-chart fe-16"></i>
          <span class="ml-3 item-text">Riwayat Skrining</span>
        </a>
      </ul>


    </nav>
  </aside>