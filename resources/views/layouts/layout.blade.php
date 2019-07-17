<!DOCTYPE html>
<html>
<head>
        @include('layouts.head')
</head>
{{-- sidebar-mini sidebar-collapse --}}
<body class="hold-transition skin-red-light sidebar-mini">
  <div class="loadingDiv" id="laodingDivUniversal"></div>
<div class="wrapper">

  <header class="main-header">

        @include('layouts.nav')

  </header>

  <!-- Left side column. contains the logo and sidebar -->
        @include('layouts.sidebar')



  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    @include('layouts.progressBar')
    <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            @yield('page-name')
            <small>@yield('page-sub')</small>
          </h1>
        </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <!-- Main row -->
      <div class="row">
        <div class="col-lg-12">
        @include('layouts.alert')
        @yield('content')

        </div>
      </div>
    </section>
    <!-- /.content -->

  </div>
  <!-- /.content-wrapper -->
        @include('layouts.footer')

  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->
        @include('layouts.modal')
        @include('layouts.script')
@stack('pageJs')

</body>
