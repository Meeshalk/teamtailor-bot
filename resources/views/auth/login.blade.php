@extends('layouts.authLayout')

@section('page-title', 'Login')
@section('content')
<body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo">
      <a href="{{ route('login') }}"><b>{{ config('app.name') }}</b>Login</a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
      <p class="login-box-msg">Login to start your session</p>
      <form method="POST" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
          @csrf
        <div class="form-group has-feedback">
          <input id="email" type="email" placeholder="Your email address" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus>

          @if ($errors->has('email'))
              <span class="invalid-feedback" role="alert">
                  <strong>{{ $errors->first('email') }}</strong>
              </span>
          @endif
          <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
          <input id="password" type="password" placeholder="Your password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

          @if ($errors->has('password'))
              <span class="invalid-feedback" role="alert">
                  <strong>{{ $errors->first('password') }}</strong>
              </span>
          @endif
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div class="row">
          <div class="col-xs-8">
            <div class="checkbox icheck">
              <label>
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-xs-4">
            <button type="submit" class="btn btn-primary btn-block btn-flat">Log in</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
      {{-- <div class="social-auth-links text-center">
        <p>- OR -</p>
        <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in using
          Facebook</a>
        <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign in using
          Google+</a>
      </div>
      <!-- /.social-auth-links --> --}}

      {{-- <a href="{{ route('password.request')}}">I forgot my password</a><br> --}}
      {{-- <a href="{{ route('register')}}" class="text-center">Register a new membership</a> --}}

    </div>
    <!-- /.login-box-body -->
  </div>
  <!-- /.login-box -->

  <!-- jQuery 3 -->
  <script src="{{ url('jquery/jquery.min.js') }}"></script>
  <!-- Bootstrap 3.3.7 -->
  <script src="{{ url('bootstrap/js/bootstrap.min.js') }}"></script>
  <!-- iCheck -->
  <script src="{{ url('iCheck/icheck.min.js') }}"></script>
  <script type="text/javascript">
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue'
    });
  });
</script>
</body>

@endsection
