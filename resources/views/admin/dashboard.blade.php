@extends('layouts.layout')
@section('page-title', 'Dashboard')
@section('page-name', 'Dashboard')
@section('page-sub', 'Control Panel')
@section('content')

@stop
@push('pageJs')
{{-- <script src="{{ url('formv/jquery.form.js')}}"></script> --}}
{{-- <script src="{{ url('formv/jquery.validate.min.js')}}"></script> --}}
<!-- Page JS -->
<script src="{{ url('theme/js/pages/dashboard.js') }}"></script>
@endpush
