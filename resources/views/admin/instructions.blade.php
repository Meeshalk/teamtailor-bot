@extends('layouts.layout')
@section('page-title', 'Instructions')
@section('page-name', 'Dashboard')
@section('page-sub', 'Instruction Panel')
@section('content')
  {{' Instructions '}}
@stop
@push('pageJs')
{{-- <script src="{{ url('formv/jquery.form.js')}}"></script> --}}
{{-- <script src="{{ url('formv/jquery.validate.min.js')}}"></script> --}}
<!-- Page JS -->
<script src="{{ url('theme/js/pages/dashboard.js') }}"></script>
@endpush
