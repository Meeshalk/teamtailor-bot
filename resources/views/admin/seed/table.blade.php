<div class="box box-info">
  <div class="box-header with-border">
    <h3 class="box-title"><i class="fa fa-th-list"></i> Seed File Listing</h3>
    {{ $seed->links() }}
  </div>
  <div class="box-body">
    {{-- <div class="table-responsive"> --}}
        <table class="table no-margin table-bordered table-condensed table-striped text-center table-fixed">
            <thead>
              <tr>
                <th style="width: 20px">#</th>
                <th>Name</th>
                <th class="desc-th">Note</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @php
                $count = 1;
              @endphp
              @foreach ($seed as $list)
                <tr>
                 <td>{{ $count }}</td>
                 <td>{{ $list->name }}</td>
                 <td class="desc-td">{{ $list->meta_desc }}</td>
                 <td>
                   <a href="{{ route('seed.process', $list->id) }}" class="btn btn-primary btn-xs mb5i">Process Domains</a>
                   <a href="{{ route('seed.show', $list->id) }}" class="btn btn-success btn-xs mb5i">Domain List</a>
                   @include('layouts.deleteForm', ['form' => ['route' => 'seed.delete', 'id' => $list->id, 'msg' => "Do you really want to delete this seed file and all other related data, like domains and jobs information etc.?"]])
                 </td>
               </tr>
               @php
                 $count ++;
               @endphp
               @endforeach
             </tbody>
           </table>

    {{-- </div> --}}
  </div>
</div>
<div class="dataTables_info addPadding5" role="status" >Showing {{$seed->firstItem()}} to {{ $seed->lastItem() }} of {{ $seed->total() }} entries </div>
