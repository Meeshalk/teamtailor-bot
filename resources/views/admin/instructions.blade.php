@extends('layouts.layout')
@section('page-title', 'Instructions')
@section('page-name', 'Dashboard')
@section('page-sub', 'Instruction Panel')
@section('content')
  <div class="row">
    <div class="col-lg-8">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">General</h3>
        </div>
        <div class="box-body">
          <div class="box-group" id="generalInstructionAccordian">

            <div class="panel">
              <div class="box-header">
                <h4 class="box-title">
                  <a data-toggle="collapse" data-parent="#generalInstructionAccordian" href="#howitworks" aria-expanded="false" class="collapsed">
                    How this application works?
                  </a>
                </h4>
              </div>
              <div id="howitworks" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                <div class="box-body">
                  This application/bot takes a domain and checks if the given domains has any association with teamtailor.com.
                   If so, then the bot will collect all the job listings with contact person name, email and phone, if available.
                   <br /> There are many ways in which this bot check a domain name and crawls to a website's links.
                </div>
              </div>
            </div>


            <div class="panel">
              <div class="box-header">
                <h4 class="box-title">
                  <a data-toggle="collapse" data-parent="#generalInstructionAccordian" href="#whenideleteseedfile" aria-expanded="false" class="collapsed">
                    What happens when I delete a seed file?
                  </a>
                </h4>
              </div>
              <div id="whenideleteseedfile" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                <div class="box-body">
                  When you delete a seed file, it deletes all the domains present in it, along with all the job listings found and all other details related to this seed file, along with the orignal file itself. And this operation is irreversible.
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>



  <div class="row">
    <div class="col-lg-8">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Seed</h3>
        </div>
        <div class="box-body">
          <div class="box-group" id="seedInstructionAccordian">

            <div class="panel">
              <div class="box-header">
                <h4 class="box-title">
                  <a data-toggle="collapse" data-parent="#seedInstructionAccordian" href="#seedFileStructure" aria-expanded="false" class="collapsed">
                    How should you structure the seed file?
                  </a>
                </h4>
              </div>
              <div id="seedFileStructure" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                <div class="box-body">
                  <ul class="list-unstyled">
                    <li><code>File Type:</code> The file type must be a <code>.csv (Comma Separated Value)</code>.</li>
                    <li><code>Structure:</code>
                      <ul>
                        <li>The file may or may not have a header row, as a first row.</li>
                        <li>If there is a header row than it must contain <code>Domain</code> or <code>Domains</code> as header name and this column must have the domains list.</li>
                        <li>If there is no header the application will read the first column.</li>
                      </ul>
                    </li>
                    <li><code>Examples:</code>
                      <div class="row">
                        <div class="col-lg-4" style="padding: 5px;"><img src="{{ url('theme/seed_file_example1.jpg') }}" height="120px"/> <div class="text-center">With header <code>domain</code>, in first column.</div></div>
                        <div class="col-lg-4" style="padding: 5px;"><img src="{{ url('theme/seed_file_example2.jpg') }}" height="120px"/> <div class="text-center">With header <code>domains</code>, in second column.</div></div>
                        <div class="col-lg-4" style="padding: 5px;"><img src="{{ url('theme/seed_file_example3.jpg') }}" height="120px"/> <div class="text-center">Without any headers, but contains domains in first column.</div></div>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <div class="panel">
              <div class="box-header">
                <h4 class="box-title">
                  <a data-toggle="collapse" data-parent="#seedInstructionAccordian" href="#uploadSeedFile" aria-expanded="false" class="collapsed">
                    How to upload seed file?
                  </a>
                </h4>
              </div>
              <div id="uploadSeedFile" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                <div class="box-body">
                  <ol>
                    <li>Click on seed, from the sidebar menu.</li>
                    <li>Enter name of the seed file.</li>
                    <li>Enter note (optinal).</li>
                    <li>Select the <code>.CSV</code> seed file, the file structure must be as mentioned above.</li>
                    <li>Select <code>Yes</code>, if the file contains headers in first row, otherwise select <code>No</code>.</li>
                    <li>Click on submit, and wait for the operation to finish.</li>
                    <li>This will add all the domains which are not already present in the database.</li>
                  </ol>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>




  <div class="row">
    <div class="col-lg-8">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Domains</h3>
        </div>
        <div class="box-body">
          <div class="box-group" id="domainInstructionAccordian">

            <div class="panel">
              <div class="box-header">
                <h4 class="box-title">
                  <a data-toggle="collapse" data-parent="#domainInstructionAccordian" href="#howmanydomains" aria-expanded="false" class="collapsed">
                    How many domains should I process at a time?
                  </a>
                </h4>
              </div>
              <div id="howmanydomains" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                <div class="box-body">
                  You can't control this manually. But you can process a seed file at a time. So, based on the Internet speed of the VPS, you can size you seed file.
                  for example, if the VPS has 100 mbps uplink then, i'll all 500-700 domains in a seed file, which will run for 6-7 hours.<br />
                  But technically you can add unlimited number of domains in a seed file, given that you'll have to continusly run your system as long as it takes to finish. <br /> Some domains takes 10 seconds to be processed and some might take upto 300 seconds. This depends on many parameters, such as;
                  <ul>
                     <li>Size of the website.</li>
                     <li>Number of links in the website.</li>
                     <li>Speed of the VPS's Internet.</li>
                     <li>Performance of the VPS.</li>
                     <li>Speed of the (remote server) wesite's Internet.</li>
                     <li>Latency of between the your VPS and the remote server (website's server).</li>
                   </ul>

                   <code>Suggetion: </code> Try with small seed files at first, then calculate average time the bot takes to process a single domain. Based on this and how long you can run the application, decide the seed file size.
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>



  <div class="row">
    <div class="col-lg-8">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Jobs</h3>
        </div>
        <div class="box-body">
          <div class="box-group" id="jobsInstructionAccordian">

            <div class="panel">
              <div class="box-header">
                <h4 class="box-title">
                  <a data-toggle="collapse" data-parent="#jobsInstructionAccordian" href="#contactpersonemailcantsee" aria-expanded="false" class="collapsed">
                    why I can't see contact person or contact email for a job listing?
                  </a>
                </h4>
              </div>
              <div id="contactpersonemailcantsee" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                <div class="box-body">
                  Some job listing don't have any contact person or email address listed. If you don't see these details you can go to the job page (link is provided in the table), and see it has these details or not. And if you see any error please contact us, we'll be more than happy to help.
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>

@stop
@push('pageJs')
{{-- <script src="{{ url('formv/jquery.form.js')}}"></script> --}}
{{-- <script src="{{ url('formv/jquery.validate.min.js')}}"></script> --}}
<!-- Page JS -->
<script src="{{ url('theme/js/pages/dashboard.js') }}"></script>
@endpush
