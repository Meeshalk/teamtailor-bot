@extends('layouts.layout')
@section('page-title', 'Jobs Dashboard')
@section('page-name', 'Dashboard')
@section('page-sub', 'Jobs Panel')
@section('content')
  <div class="row">
    <div class="col-lg-12">
      @include('admin.jobs.tableFull')
    </div>
  </div>
@stop
