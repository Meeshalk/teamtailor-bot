<div class="box box-primary">
  <div class="box-header with-border">
    <h3 class="box-title"><i class="fa fa-plus"></i> One by One ajax Process of seed file {<b>{{$seed->name}}</b>}</h3>
  </div>
  <div class="box-body">
    <form class="form ajax-table" id="domainProcess" action="{{route('domain.process.chunk')}}" method="POST" novalidate autocomplete="off">
      @csrf
      <input type="hidden" name="seed_id" value="{{$seed->id}}" />
      <h4><b>Overall progress</b></h4>
      <div class="progress progress-lg">
        <div class="progress-bar progress-bar-success progress-bar-striped" id="mainProgress" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
      </div>
      <h4><b>Current Progress</b></h4>
      <div class="progress progress-lg">
        <div class="progress-bar progress-bar-info progress-bar-striped" id="chunkProgress" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
      </div>

      <div class="col-lg-12 mb10i">
        <span class="pull-left">{{ "We have {$seed->domains_count} domains in this seed file."}}</span>
        <span class="pull-right text-green processed-text" id="processedUpto">Processed <code>&nbsp;{{$current_step}}&nbsp;</code> out of <code>&nbsp;{{$step*$chunk_size}}&nbsp;</code> domains.</span>
      </div>

      <div class="col-lg-3">
        <div class="form-group">
          <label for="chunk_size">Chunk Size</label>
          <input type="text" class="form-control" id="chunk_size" name="chunk_size" readonly value="{{$chunk_size}}">
        </div>
      </div>

      <div class="col-lg-3">
        <div class="form-group">
            <label for="step">Steps</label>
            <input type="text" class="form-control" name="steps" id="steps" readonly value="{{$step}}">
        </div>
      </div>

      <div class="col-lg-3">
        <div class="form-group">
            <label for="current_step">Current Step</label>
            <input type="text" class="form-control" name="current_step" id="current_step" readonly value="{{ $current_step ?? '0'}}">
        </div>
      </div>

      <div class="col-lg-3">
        <input type="submit" class="btn btn-success form-control custom-process-btn" value="Start Processing">
      </div>


    </form>
  </div>
</div>
