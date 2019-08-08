@extends('layouts.layout')
@section('page-title', 'Domains Jobs')
@section('page-name', 'Dashboard')
@section('page-sub', $domain->domain.' Jobs')
@section('content')
  <div class="row">
    <div class="col-lg-12">
      @include('admin.jobs.table')
    </div>
  </div>
@stop
@push('pageJs')

@endpush
