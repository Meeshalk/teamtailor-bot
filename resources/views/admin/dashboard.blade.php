@extends('layouts.layout')
@section('page-title', 'Dashboard')
@section('page-name', 'Dashboard')
@section('page-sub', 'Control Panel')
@section('content')
  {{ 'In this page, important features, statistics, graphs, statistical charts etc can be added in next phase of this project.'}}
@stop
@push('pageJs')
{{-- <script src="{{ url('formv/jquery.form.js')}}"></script> --}}
{{-- <script src="{{ url('formv/jquery.validate.min.js')}}"></script> --}}
<!-- Page JS -->
<script src="{{ url('theme/js/pages/dashboard.js') }}"></script>
@endpush
