@extends('errors::illustrated-layout')

@section('code', '404')
@section('title', __('Page Not Found'))

@section('image')
    <div style="background-image: url({{ asset('/svg/404.svg') }});" class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center">
    </div>
@endsection

@if ($exception->getMessage() != false)
    @section('message', __($exception->getMessage()))
@elseif (isset($error) && $error['message'] != false)
    @section('message', __($error['message']))
@else
    @section('message', __('Sorry, the page you are looking for could not be found.'))
@endif
