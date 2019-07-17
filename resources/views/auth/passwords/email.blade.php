@extends('layouts.authLayout')
@section('page-title', 'Send Reset Link')
@section('content')
<body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo">
      <a href="{{ route('password.request') }}"><b>{{config('app.name')}}</b>Reset Password</a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
      <p class="login-box-msg">Recive a password reset link</p>
      @if (session('status'))
          <div class="alert alert-success" role="alert">
              {{ session('status') }}
          </div>
      @endif
      <form method="POST" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
          @csrf
        <div class="form-group has-feedback">
          <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>

          @if ($errors->has('email'))
              <span class="invalid-feedback" role="alert">
                  <strong>{{ $errors->first('email') }}</strong>
              </span>
          @endif
          <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>
        <div class="row">
          <div class="col-xs-4">
          </div>
          <!-- /.col -->
          <div class="col-xs-8">
            <button type="submit" class="btn btn-primary btn-block btn-flat">Send Password Reset Link</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
      <br />
      <a href="{{ route('login') }}" class="text-center">Go to login page</a>
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
