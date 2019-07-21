<div class="box box-primary">
  <div class="box-header with-border">
    <h3 class="box-title"><i class="fa fa-plus"></i> Upload Seed File</h3>
  </div>
  <div class="box-body">
    <form class="form ajax-table" id="addSeedFile" action="{{route('seed.store')}}" method="POST" enctype="multipart/form-data" novalidate autocomplete="off">
      @csrf
      <div class="form-group">
        <label for="name">Seed Name</label>
        <input type="text" class="form-control" name="name" id="name" placeholder="Enter seed name">
      </div>

      <div class="form-group">
        <label for="note">Note</label>
        <span class="pull-right counter_box">Characters: 0</span>
        <textarea class="form-control" name="note" rows="3" id="note" placeholder="Write a note for the upload (optional)"></textarea>
      </div>

      <div class="form-group">
        <label for="file">File (CSV Only)</label>
        <span class="pull-right counter_box">Total Size: 0</span>
        <input type="file" class="" data-max="52428800" name="file" id="file" accept=".csv">
        <br />
        <div class="iCheck">
          <label>
            Does this file has headers in first line?<br />
            <input type="radio" class="truefalse" name='header' value="TRUE" checked> Yes &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" class="truefalse" name='header' value="FALSE" > No
          </label>
        </div>

        <div id="fileErrors"></div>
      </div>
      <input type="submit" class="btn btn-success" value="Submit">
    </form>
  </div>
</div>
