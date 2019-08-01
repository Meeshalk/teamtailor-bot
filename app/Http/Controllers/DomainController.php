<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Domain;
use App\Seed;
use Pdp\Cache;
use Pdp\CurlHttpClient;
use Pdp\Manager;
use Pdp\Rules;
use App\Http\Controllers\BotController as BC;

class DomainController extends Controller
{

    protected $baseView = 'admin.domains.';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
      $domain = Domain::with('domainable')->paginate(10);
      return view($this->baseView.'index', ['domain' => $domain]);
    }

    public function chunkProcessAjax($id){
      try {
        $seed = Seed::withCount('domains')->findOrFail($id);
        return view($this->baseView.'process', ['seed' => $seed, 'step' => $seed->domains_count, 'chunk_size' => 1]);
      } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

      }
      abort(404, 'The seed file does not exists. Resource not found.');
    }

    public function process(Request $ajax){
      $in = $ajax->except('_token');
      try {
        $cStep = $in['current_step'];
        $cSize = $in['chunk_size'];
        $skip = ($cStep*$cSize);
        if($in['steps'] > $cStep){
          $seed = Seed::with(['domains' => function($query) use ($skip, $cSize) {
            $query->skip($skip)->take($cSize)->orderBy('id', 'asc');
          }])->findOrFail($in['seed_id']);
          //process here
          $res = null;
          foreach ($seed->domains as $domain) {
            $tTStatus = $this->findJobsPage($domain->domain);
            //$res = var_dump($tTStatus)
            // if($tTStatus === false)
              // continue;
            $res[] = Domain::where('id', $domain->id)->update($tTStatus);
          }
          $newStep = $cStep+1;
          echo json_encode(['res' => $tTStatus, 'url' => route('domain.process.chunk'), 'seedId' => $in['seed_id'], 'type' => 'POST', 'steps' => $in['steps'], 'currentStep' => $newStep, 'chunkSize' => $cSize, 'keepGoing' => 1]);
        }else{
          echo json_encode(['seedId' => $in['seed_id'], 'steps' => $in['steps'], 'chunkSize' => $in['chunk_size'], 'keepGoing' => 0, 'status' => 1]);
        }
      } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        abort(404, 'The seed file does not exists. Resource not found.');
      }

    }


    public function findJobsPage($domain){
      set_time_limit(3600);
      $time = microtime(true);
      $bot = new BC;
      //fimding subdomain association with teamtailor.com eg. domain.teamtailor.com
      $bySubDomain = $bot->findTtAssociation($domain, 'subdomain');
      if($bySubDomain !== false && $bySubDomain['status'] === true){
         unset($bySubDomain['status']);
         $bySubDomain['completed_in'] = (microtime(true) - $time);
         return $bySubDomain;
      }

      $byDomain = $bot->findTtAssociation($domain, 'domain', 'curl', false);
        unset($byDomain['status']);
        $byDomain['completed_in'] = (microtime(true) - $time);
        return $byDomain;
    }

    public function testFindJobsPage($domain){
      set_time_limit(3600);
      $time = microtime(true);
      $bot = new BC;
      $bySubDomain = $bot->findTtAssociation($domain, 'subdomain');
      if($bySubDomain !== false && $bySubDomain['status'] === true){
        $bySubDomain['completed_in'] = (microtime(true) - $time);
        echo "<pre>",print_r($bySubDomain),"</pre>";
        exit;
      }

      $byDomain = $bot->findTtAssociation($domain, 'domain', 'curl', false);
      $byDomain['completed_in'] = (microtime(true) - $time);
      echo "<pre>",print_r($byDomain),"</pre>";
      echo "<pre>",print_r(array_unique($bot->globaLink)),"</pre>";

      echo (microtime(true) - $time) . ' seconds<br />';
    }

}
