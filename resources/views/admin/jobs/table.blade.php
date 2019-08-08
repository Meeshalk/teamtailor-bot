<div class="box box-info">
  <div class="box-header with-border">
    <h3 class="box-title"><i class="fa fa-th-list"></i> Jobs Listing for {<b>{{$domain->domain}}</b>}</h3>
    {{ $jobs->links() }}
  </div>
  <div class="box-body">
    <div class="table-responsive">
        <table class="table no-margin table-bordered table-condensed table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Title</th>
                <th>Link</th>
                <th>Contact Person</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @php
                $count = 1;
              @endphp
              @foreach ($jobs as $list)
                <tr>
                 <td>{{ $count }}</td>
                 <td>{{ $list->title }}</td>
                 <td>
                    @if ($list->link != false)
                      @php
                      $jd = $list->link;
                        $jd = str_replace('http://', '', $jd);
                        $jd = str_replace('https://', '', $jd);
                        $jd = substr_replace($jd, '', strpos($jd, '/'), strlen('/'));
                      @endphp
                      <a href="{{$list->link}}" title="{{$list->link}}" target="_blank">{{$jd}}</a>
                    @else
                      {{ '' }}
                    @endif

                 </td>

                 <td>@if($list->contact_person == 'not found'){{'Not Found'}}@else{{ $list->contact_person }}@endif</td>
                 <td>@if($list->contact_email == 'not found'){{'Not Found'}}@else{{ $list->contact_email }}@endif</td>
                 <td>@if($list->contact_tel == 'not found'){{'Not Found'}}@else{{ $list->contact_tel }}@endif</td>
                 <td>

                 </td>
               </tr>
               @php
                 $count ++;
               @endphp
               @endforeach
             </tbody>
           </table>

    </div>
  </div>
</div>
<div class="dataTables_info addPadding5" role="status" >Showing {{$jobs->firstItem()}} to {{ $jobs->lastItem() }} of {{ $jobs->total() }} entries </div>
