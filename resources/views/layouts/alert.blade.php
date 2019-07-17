@if(Session::has('alert'))
  <div class="alert {{session('alert')}} alert-dismissible fade in" id="global-alert">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times</a>
    {{session('message')}}
  </div>
@endif
<div id="global-alert-js-block">

</div>
