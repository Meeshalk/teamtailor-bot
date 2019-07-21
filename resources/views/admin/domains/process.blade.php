@extends('layouts.layout')
@section('page-title', 'Domain Dashboard')
@section('page-name', 'Dashboard')
@section('page-sub', 'Domain Panel')
@section('content')
  <div class="row">
    <div class="col-lg-12">
      @include('admin.domains.ajaxProcess')
    </div>
  </div>
@stop
@push('pageJs')
  <script src="{{ url('theme/js/pages/processAjax.js') }}"></script>
@endpush
