@extends('layouts.layout')
@section('page-title', 'Seed Domains')
@section('page-name', 'Dashboard')
@section('page-sub', $seed->name.' Domains')
@section('content')
  <div class="row">
    <div class="col-lg-12">
      @include('admin.domains.table')
    </div>
  </div>
@stop
@push('pageJs')

@endpush
