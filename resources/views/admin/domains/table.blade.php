<div class="box box-info">
  <div class="box-header with-border">
    <h3 class="box-title"><i class="fa fa-th-list"></i> Domain Listing</h3>
    {{ $domain->links() }}
  </div>
  <div class="box-body">
    <div class="table-responsive">
        <table class="table no-margin table-bordered table-condensed table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Domain</th>
                <th>Job Site</th>
                <th>Verified</th>
                <th>Job Page</th>
                <th>Job #</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @php
                $count = 1;
              @endphp
              @foreach ($domain as $list)
                <tr>
                 <td>{{ $count }}</td>
                 <td>{{ $list->domain }}</td>
                 <td>
                    @if ($list->redirected_url != false)
                      @php
                      $jd = $list->redirected_url;
                        $jd = str_replace('http://', '', $jd);
                        $jd = str_replace('https://', '', $jd);
                        $jd = substr_replace($jd, '', strpos($jd, '/'), strlen('/'));
                      @endphp
                      <a href="{{$list->redirected_url}}" title="{{$list->redirected_url}}" target="_blank">{{$jd}}</a>
                    @else
                      {{ '' }}
                    @endif

                 </td>
                 @if ($list->verified == 1)
                   <td><span class="badge bg-green tags">{{ 'Yes' }}</span></td>
                 @else
                   <td><span class="badge bg-red tags">{{ 'No' }}</span></td>
                 @endif

                 <td>
                   @if ($list->job_page != false)
                     @php
                     $jp = $list->job_page;
                       $jp = str_replace('http://', '', $jp);
                       $jp = str_replace('https://', '', $jp);
                     @endphp
                     <a href="{{$list->job_page}}" title="{{$list->job_page}}" target="_blank">{{$jp}}</a>
                   @else
                     {{ '' }}
                   @endif
                 </td>
                 <td>{{ $list->job_count }}</td>
                 <td>
                   @if (isset($list->job_count) && $list->job_count != 0)
                     <a href="{{ route('domain.show', $list->id) }}" class="btn btn-success btn-xs mb5i">Jobs</a>
                   @endif

                   {{-- @include('layouts.deleteForm', ['form' => ['route' => 'domain.delete', 'id' => $list->id, 'msg' => "Do you really want to delete this domain and all other related data, like jobs information and contact details etc.?"]]) --}}
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
<div class="dataTables_info addPadding5" role="status" >Showing {{$domain->firstItem()}} to {{ $domain->lastItem() }} of {{ $domain->total() }} entries </div>
