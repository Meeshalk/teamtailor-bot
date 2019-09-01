<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use League\Csv\Reader as CsvReader;
use League\Csv\Writer as CsvWriter;
use League\Csv\CannotInsertRecord;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Session;
use Pdp\Cache;
use Pdp\CurlHttpClient;
use Pdp\Manager;
use Pdp\Rules;
use App\Seed;
use App\Domain;
use DB;

class SeedController extends Controller
{
    protected $baseView = 'admin.seed.';
    protected $urlPatt = "/^(?<protocol>https?:\/\/)?(?<domain>[a-z0-9.-]+)(?<hyphne>\/)?/";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
      return view($this->baseView.'index', ['seed' => Seed::paginate(10)]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $ajax){
      set_time_limit(3600);
      $in = $ajax->except('_token');
      $domains = $record = [];
      $csv = $fileName = null;

      if(isset($in['header'])){
        $in['header'] = $in['header'] == 'TRUE'?1:0;
      }else{
        $in['header'] = 0;
      }

      if(!isset($in['csv'])){
        echo json_encode(['status' => 0, 'data' => '<div class="text-red">No file is detected by us. please add a csv seed file!</div>']);
        exit;
      }

      if($in['csv']->getClientOriginalExtension() != 'csv'){
        echo json_encode(['status' => 0, 'data' => '<div class="text-red">The file uploaded is not supported!</div>']);
        exit;
      }
      unset($in['csv']);

      if(empty($in['name'])){
        echo json_encode(['status' => 0, 'data' => '<div class="text-red">The name field is enpty.</div>']);
        exit;
      }

      $in['name'] = ucwords(strtolower(trim($in['name'])));

      if(empty($in['note'])){
        unset($in['note']);
      }else{
        $in['note'] = Controller::sentenceCase($in['note']);
      }

      $fileName = md5($in['name']).".csv";
      if($ajax->file('csv')->storeAs('/public/seeds', $fileName) == false){
        echo json_encode(['status' => 0, 'data' => '<div class="text-red">Error: While reading or saving the csv file.</div>']);
      }
      $in['file'] = 'seeds/'.$fileName;

      try {
        $csv = CsvReader::createFromPath("storage/".$in['file'], 'r');
        if($in['header'] === 1){
          $csv->setHeaderOffset(0);
          foreach ($csv->getRecords() as $line) {
            if(isset($line['domains'])){
              $domains[] = trim($line['domains']);
            }else if(isset($line['Domains'])){
              $domains[] = trim($line['Domains']);
            }else if(isset($line['DOMAINS'])){
              $domains[] = trim($line['DOMAINS']);
            }else if(isset($line['domain'])){
              $domains[] = trim($line['domain']);
            }else if(isset($line['Domain'])){
              $domains[] = trim($line['Domain']);
            }else if(isset($line['DOMAIN'])){
              $domains[] = trim($line['DOMAIN']);
            }else{
              $domains[] = trim($line[0]);
            }
          }
        }else{
          foreach ($csv->getRecords() as $line) {
            $domains[] = trim($line[0]);
          }
        }
      } catch (\Exception $e) {
         echo json_encode(['status' => 0, 'data' => '<div class="text-red"><h4 class="text-red">Internal Error: 73656564x01050</h4><br />Message: '.$e->getMessage().'</div>']);
         exit;
      }

      if(!is_array($domains)){
        echo json_encode(['status' => 0, 'data' => '<div class="text-red">Error: Domains Not Found.<br />Please check <a href="'.route('instructions').'" class="link" target="_blank">instructions</a> on how the file must be formatted.</div>']);
        exit;
      }

      $manager = new Manager(new Cache(), new CurlHttpClient());
      $rules = $manager->getRules();
      foreach ($domains as $dk => $domain) {
        preg_match_all(config('teamtailor.patterns.domain'), $domain, $hl);
        if(empty($hl['domain'][0]))
          continue;

        $host = $hl['domain'][0];
        $d = $rules->resolve($host);
        if(!$d->isKnown() && !$d->isICANN() && !$d->isResolvable()){
          continue;
        }
        $record[] = ['domain' => $d->getRegistrableDomain(), 'orignal_url' => $domain];
      }


      if(!is_array($record) || empty($record)){
        echo json_encode(['status' => 0, 'data' => '<div class="text-red">Error: Domains Are Not Valid. Please read <a href="'.route('instructions').'" class="link" target="_blank">instructions</a> to know more.</div>']);
        exit;
      }

      DB::beginTransaction();
      try {
        $seed = Seed::create($in);
        if($seed == false){
          echo json_encode(['status' => 0, 'data' => '<div class="text-red">Error: Somthing went wrong while creating seed record.</div>']);
          DB::rollback();
          exit;
        }
      } catch (\Illuminate\Database\QueryException $f) {
        DB::rollback();
        if($f->errorInfo[1] == 1062){
          echo json_encode(['status' => 1, 'data' => "<div class='text-red'>Error: the seed data with name: <b>{$in['name']}</b>, already exists.</div>"]);
          exit;
        }
        echo json_encode(['status' => 0, 'errorMsg' => $f->getMessage(), 'data' => '<div class="text-red">Error: Somthing went wrong with database while creating seed record. <br />Please check the name is unique than erlier names.</div>']);
        exit;
      }

      $count = $rejected = 0;
      foreach ($record as $r) {
        $inst = false;
        try {
          $inst = $seed->domains()->create($r);
          if($inst != false)
            $count++;
        } catch (\Illuminate\Database\QueryException $e) {
          if($e->errorInfo[1] == 1062){
            $rejected++;
          }
          continue;
        }
      }
      if($count == 0){
        DB::rollback();
        echo json_encode(['status' => 1, 'data' => "<div class='text-red'>Error: No unique domain found in this seed file.</div><br />".'Please read <a href="'.route('instructions').'" class="link" target="_blank">instructions</a> to know more.']);
        exit;
      }

      DB::commit();
      echo json_encode(['status' => 1, 'data' => "<div class='text-green'>Success: The seed file is processed, <code>&nbsp;<b>{$count}</b>&nbsp;</code> domains are stored and <code>&nbsp;<b>{$rejected}</b>&nbsp;</code> are rejected because of duplicate records.</div>"]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Seed  $seed
     * @return \Illuminate\Http\Response
     */
    public function show($id){
      try {
        $seed = Seed::findOrFail($id);
        return view($this->baseView.'show', ['seed' => $seed, 'domain' => $seed->domains()->paginate(10)]);
      } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        abort(404, 'The seed domains does not exists. Resource not found.');
      }
    }


    public function export($id){
      try {
        $seed = Seed::findOrFail($id);
        try {
          $writer = CsvWriter::createFromPath("storage/temp.csv", 'w+');
          $writer->insertOne(['domains with teamtailor association', '# of jobs found']);
          foreach ($seed->domains()->where('verified', '=', 1)->select('domain', 'job_count')->get() as $w) {
            $writer->insertOne([$w->domain, $w->job_count]);
          }
          return response()->download('storage/temp.csv', "verified_".$seed->name.".csv")->deleteFileAfterSend();
        } catch (CannotInsertRecord $e) {

        }
      } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        abort(404, 'The seed file does not exists. Resource not found.');
      }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Seed  $seed
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
      set_time_limit(3600);
      try {
        $seed = Seed::findOrFail($id);
        $name = $seed->name;
        $file = $seed->file;
        $allDomains = $seed->domains()->get();
        foreach ($allDomains as $domain) {
          $domain->jobs()->delete();
        }
        $seed->domains()->delete();
        $seed->delete();
        Storage::delete('public/'.$file);
        Session::flash('alert', 'alert-success');
        return Redirect::route('seed')->with('message', "Seed Data: {$name}, has been successfully deleted.");
      } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        abort(404, 'The seed domains does not exists. Resource not found.');
      }
    }


    //test acurl
    // $curl = new Acurl;
    // $req = $curl->newRequest('get', 'http://'.$d->getContent())->setOption(CURLOPT_FOLLOWLOCATION, true);
    // $response = $req->send();
    // echo "domain: ". $d->getContent();
    // echo "<br />";
    // echo "<pre>";
    // //print_r($response->headers);
    // echo "<br />";
    // print_r($response->info);
    // echo "</pre>";

    //test CURL

}
