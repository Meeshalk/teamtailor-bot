@extends('layouts.authLayout')
@section('name', 'Reset Your Password')
@section('content')
  <body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo">
      <a href="{{ route('login') }}"><b>{{ config('app.name') }}</b>Reset Password</a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
      <p class="login-box-msg">Reset your password</p>
      <form method="POST" action="{{ route('password.request') }}" aria-label="{{ __('Reset Password') }}">
          @csrf
          <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-group has-feedback">
          <input id="email" type="email" placeholder="Your email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ $email ?? old('email') }}" required>

          @if ($errors->has('email'))
              <span class="invalid-feedback" role="alert">
                  <strong>{{ $errors->first('email') }}</strong>
              </span>
          @endif
          <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
          <input id="password" type="password" placeholder="Enter new password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required autofocus>

          @if ($errors->has('password'))
              <span class="invalid-feedback" role="alert">
                  <strong>{{ $errors->first('password') }}</strong>
              </span>
          @endif
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>

        <div class="form-group has-feedback">
          <input id="password-confirm" type="password"  placeholder="Confirm the new password" class="form-control" name="password_confirmation" required>
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>

        <div class="row">
          <div class="col-xs-6">
            {{-- <div class="checkbox icheck">
              <label>
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
              </label>
            </div> --}}
          </div>
          <!-- /.col -->
          <div class="col-xs-6">
            <button type="submit" class="btn btn-primary btn-block btn-flat">Reset Password</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
      <br />


      {{-- <a href="{{ route('register')}}" class="text-center">Register a new membership</a> --}}

    </div>
    <!-- /.login-box-body -->
  </div>
  <!-- /.login-box -->

  <!-- jQuery 3 -->
  <script src="{{ url('jquery/jquery.min.js') }}"></script>
  <!-- Bootstrap 3.3.7 -->
  <script src="{{ url('bootstrap/js/bootstrap.min.js') }}"></script>

</body>
@endsection
