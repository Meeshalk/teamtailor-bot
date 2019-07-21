<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Domain;
use App\Seed;

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
          $newStep = $cStep+1;
          echo json_encode(['url' => route('domain.process.chunk'), 'seedId' => $in['seed_id'], 'type' => 'POST', 'steps' => $in['steps'], 'currentStep' => $newStep, 'chunkSize' => $cSize, 'keepGoing' => 1]);
        }else{
          echo json_encode(['seedId' => $in['seed_id'], 'steps' => $in['steps'], 'chunkSize' => $in['chunk_size'], 'keepGoing' => 0, 'status' => 1]);
        }
      } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        abort(404, 'The seed file does not exists. Resource not found.');
      }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Domain  $domain
     * @return \Illuminate\Http\Response
     */
    public function show(Domain $domain)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Domain  $domain
     * @return \Illuminate\Http\Response
     */
    public function edit(Domain $domain)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Domain  $domain
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Domain $domain)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Domain  $domain
     * @return \Illuminate\Http\Response
     */
    public function destroy(Domain $domain)
    {
        //
    }
}
