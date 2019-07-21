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
                <th>Seed File</th>
                <th>Reachable URL</th>
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
                 <td><a href="{{ route('seed.show', $list->domainable->id) }}">{{ $list->domainable->name }}</a></td>
                 {{-- <td><a href="{{ route('seed.show', $list->domainable->id) }}" class="btn btn-success btn-xs mb5i">{{ $list->domainable->name.' List' }}</a></td> --}}
                 <td>{{ $list->redirected_url }}</td>
                 <td>{{ $list->verified }}</td>
                 <td>{{ $list->job_page }}</td>
                 <td>{{ $list->job_count }}</td>
                 <td>
                   <a href="{{ route('domain.show', $list->id) }}" class="btn btn-success btn-xs mb5i">Jobs</a>
                   @include('layouts.deleteForm', ['form' => ['route' => 'domain.delete', 'id' => $list->id, 'msg' => "Do you really want to delete this domain and all other related data, like jobs information and contact details etc.?"]])
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
