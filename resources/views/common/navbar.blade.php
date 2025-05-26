<link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
<nav class="navbar navbar-expand-lg custom-navbar">
  <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
    <img src="https://res.cloudinary.com/dhis8yzem/image/upload/v1747887624/logo_2_o7zkws.png" class="logo-img mr-2"
      alt="Logo">
    <strong>Motorbike Repair</strong>
  </a>

  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNavbar"
    aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="mainNavbar">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
        <a class="nav-link" href="{{ url('/bookings') }}" data-page="bookings">Đặt lịch</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ url('/services') }}" data-page="services">Dịch vụ</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ url('/employees') }}" data-page="employees">Thợ sửa</a>
      </li>
    </ul>

    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <span class="navbar-text mr-3">Chào user</span>
      </li>
      <li class="nav-item">
        <form method="POST" style="display: inline;">
          @csrf
          <button class="btn btn-outline-light btn-sm" type="submit">Đăng xuất</button>
        </form>
      </li>
    </ul>
  </div>
</nav>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.nav-link').forEach(function (link) {
      const page = link.dataset.page;
      const parent = link.closest('.nav-item');
      if (page && window.location.pathname.includes(page)) {
        parent.classList.add('active');
      } else {
        parent.classList.remove('active');
      }
    });
  });
</script>