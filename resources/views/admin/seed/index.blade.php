@extends('layouts.layout')
@section('page-title', 'Seed Dashboard')
@section('page-name', 'Dashboard')
@section('page-sub', 'Seed Panel')
@section('content')
  <div class="row">
    <div class="col-lg-4">
      @include('admin.seed.createForm')
    </div>

    <div class="col-lg-8">
      @include('admin.seed.table')
    </div>
  </div>
@stop
@push('pageCss')
  <link rel="stylesheet" href="{{ url('iCheck/square/blue.css') }}">
@endpush
@push('pageJs')
<script src="{{ url('formv/jquery.form.js')}}"></script>
<script src="{{ url('formv/jquery.validate.min.js')}}"></script>
<!-- Page JS -->
<script src="{{ url('theme/js/pages/seed.js') }}"></script>
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
@endpush
