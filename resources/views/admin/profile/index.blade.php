@extends('layouts.layout')
@section('page-title', 'Jobs Dashboard')
@section('page-name', 'Profile')
@section('page-sub', Auth::user()->name)
@section('content')
  <div class="row">
    <div class="col-lg-4">
      @include('admin.profile.passwordChange')
    </div>
  </div>
@stop
@push('pageJs')
  <script src="{{ url('formv/jquery.form.js')}}"></script>
  <script src="{{ url('formv/jquery.validate.min.js')}}"></script>
  <script src="{{ url('theme/js/pages/profile.js') }}"></script>
@endpush
