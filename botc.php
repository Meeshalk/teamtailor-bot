<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Curl\Curl;
use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Page;

class BotController extends Controller
{

  protected $content;

  public function __construct(){
    $this->content = file_get_contents(public_path('tt.html'));
  }

  public function findTtAssociation($domain, $method = 'subdomain', $type = 'curl'){
    if($method == 'subdomain')
      $domain = explode('.', $domain)[0].'.teamtailor.com';

    $content = $this->getStatusAndContent($domain, $type);
    if(is_bool($content) && $content === false)
      return false;

    if(is_string($content['content']) && $this->findTextInDocument($content['content'], 'p', 'teamtailor', config('teamtailor.keywords.isTt'))){
      $url = $this->findTtJobPage($content['content'], 'a', ['logo', 'hidden-background']);
      similar_text($url, $content['info']['url'], $pcent);
      if($pcent >= 92.0){
        $jobPage = $this->verifyJobPage($content['content'], config('teamtailor.keywords.jobPage'));
        return [
          'status' => true,
          'method' => 'subdomain association with teamtailor.com',
          'redirects' => $content['info']['redirect_count'],
          'redirected_from' => "http://$domain",
          'redirected_url' => $content['info']['url'],
          'job_url' => $url,
          'secure' => ($content['info']['primary_port'] == 443?1:0),
          'verified' => 1,
          'job_page' => $url.$jobPage,
          'type' => 'unknown'
        ];
      }
    }
      return false;
  }


  public function findTtByDomain($domain, $type = 'curl'){
    $content = $this->getStatusAndContent($domain, $type);
    if(is_bool($content) && $content === false)
      return false;

    var_dump($content);

  }

  private function getStatusAndContent($domain, $type){
    if($type == 'headless'){
      return $this->checkStatusAndContent($this->getSiteContentHeadless($domain));
    }
    return $this->checkStatusAndContent($this->getSiteContents($domain));
  }

  private function checkStatusAndContent($content){
    if($content == false)
      return false;

    if(is_array($content) && $content['status'] == '999'){
      $this->getStatusAndContent($content['domain'], 'curl');
    }

    if($content['status'] == '200')
      return $content;

    return false;
  }


  private function verifyJobPage($content, $match){
    $xpath_main = $this->evaluate($content, '//*[contains(concat(" ", normalize-space(@class), " "),"career-site-navigation")]/descendant::a[contains(concat(" ", normalize-space(@href), " "),"")]/@href');
    if($xpath_main->length > 0 ){
      foreach ($xpath_main as $node_main) {
        if(in_array(str_replace('/', '', strtolower(trim($node_main->textContent))), $match))
          return $node_main->textContent;
      }

    }

    $xpath = $this->evaluate($content, '//descendant::a[contains(concat(" ", normalize-space(@href), " "),"")]/@href');
    if($xpath->length == 0){
      return false;
    }

    foreach ($xpath as $key => $node) {
      if(in_array(str_replace('/', '', strtolower(trim($node->textContent))), $match))
        return $node->textContent;

        //        echo "<pre>",print_r($node),"</pre>";
    }
  }


  private function findTextInDocument($content, $parent, $search, $match){
    $search = $this->getUniqueLowerUpperCase($search);
    $xpath = $this->evaluate($content, '//'.$parent.'[text()[contains(translate(., "'.$search['upper'].'", "'.$search['lower'].'"), "'.$search['search'].'")]]');
    if($xpath->length == 0){
      return false;
    }

    foreach ($xpath as $key => $node) {
      if(in_array(strtolower(trim($node->textContent)), $match))
        return true;
    }
    return false;
  }


  private function findTtJobPage($content, $parent, $search){
    $xpath = $this->evaluate($content, '//'.$parent.'[contains(concat(" ", normalize-space(@class), " "),"'.$search[0].'") or contains(concat(" ", normalize-space(@class), " "),"'.$search[1].'")]/@href');
    if($xpath->length != 1){
      return false;
    }
    return $xpath->item(0)->textContent;
  }


  private function evaluate($content, $expression){
    $DOM = new \DOMDocument('1.0', 'UTF-8');
    $internalErrors = libxml_use_internal_errors(true);
    $DOM->loadHTML($content);
    libxml_use_internal_errors($internalErrors);
    $xpath = new \DOMXPath($DOM);
    //echo "<pre>",print_r($xpath->evaluate($expression)->length),"</pre>";
    return $xpath->evaluate($expression);

  }

  private function expression($d, $mcld = false){
    $exp = "";
    foreach ($d as $k => $v) {
      if($k == 'parent'){
        $exp = ".//{$d['parent']['element']}";
      }else if($k == 'child' && !$mcld){
        $exp .= "/{$d['child']['element']}";
      }else if($k == 'child' && $mcld){
        $exp .= "//{$d['child']['element']}";
      }else if($k == 'child2'){
        $exp .= "/{$d['child2']['element']}";
      }

      if(isset($v['match'])){
        switch ($v['match']['type']) {
          case 'class':
            $exp .= "[contains(concat(' ', normalize-space(@class), ' '),'".$v['match'][$v['match']['type']]."')]";
            break;

          case 'text':
            $exp .= "[contains(text(),'".$v['match'][$v['match']['type']]."')]";
            break;

          case 'regex':
            //$exp .= "[contains(text(),'".$v['match'][$v['match']['type']]."')]";
            break;

          case 'id':
            $exp .= "[contains(concat(' ', normalize-space(@id), ' '),'".$v['match'][$v['match']['type']]."')]";
            break;

          default:
            // code...
            break;
        }
      }
    }
    return $exp;
  }


  public function getSiteContents($domain){
    $curl = new Curl();
    $curl->setUserAgent('TechkumarJobsBot /0.1.2 (+http://projects.techkumar.in/bots/job)');
    $curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
    $curl->setOpt(CURLOPT_MAXREDIRS, 7);
    $curl->get('http://'.$domain);

    // if($curl->getErrorCode() == 404){
    //   return ['status' => '404', 'content' => $curl->getRawResponse(), 'info' => $curl->getInfo()];
    // }

    if(in_array($curl->getErrorCode(), config('teamtailor.curl.errors')))
      return false;

    if($curl->getInfo()['size_download'] < 3000)
      return false;

    if($curl->getErrorCode() == 0){
      return ['status' => '200', 'content' => $curl->getRawResponse(), 'info' => $curl->getInfo()];
    }

    return false;
  }

  private function getSiteContentHeadless($domain){
    set_time_limit(60);
    $browserFactory = new BrowserFactory();
    $browser = $browserFactory->createBrowser([
        'headless'        => true,
        'sendSyncDefaultTimeout' => 60000,
        'connectionDelay' => 0,
        'ignoreCertificateErrors' => true,
        'keepAlive' => true
    ]);
    $page = $browser->createPage();
    $page->navigate('http://'.$domain)->waitForNavigation();
    $evaluation = $page->evaluate('document.documentElement.outerHTML');
    $content = $evaluation->getReturnValue();
    $browser->close();
    $cLength = strlen($content);
    if($cLength > 1000)
      return ['status' => '200', 'length' => $cLength, 'content' => $content];
    return ['status' => '999', 'length' => $cLength, 'content' => $content, 'domain' => $domain];
  }

  private function getUniqueLowerUpperCase($in){
    return ['upper' => strtoupper(count_chars($in, 3)), 'lower' => strtolower(count_chars($in, 3)), 'search' => $in];
  }
}
