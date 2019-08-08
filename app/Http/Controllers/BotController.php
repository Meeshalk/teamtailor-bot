<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Curl\Curl;
use Pdp\Cache;
use Pdp\CurlHttpClient;
use Pdp\Manager;
use Pdp\Rules;
use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Page;

class BotController extends Controller
{
  public $globaLink;

  public function __construct(){
    $this->globaLink = [];
  }

  public function findTtAssociation($domain, $method = 'subdomain', $type = 'curl', $level2 = false){
    $methodStr = 'direct domain association with teamtailor';
    $websiteType = 'recruiter';
    $charset = [];
    if($method == 'subdomain'){
      $domain = explode('.', $domain)[0].'.teamtailor.com';
      $methodStr = 'subdomain association with teamtailor.com';
      $websiteType = 'unknown';
    }

    $content = $this->getStatusAndContent($domain, $type);
    if(is_bool($content) && $content === false)
      return false;

    //setting charset
    $this->setCharset($content['info']['content_type']);

    //by sub domain or main domain (based on method)
    $stepOne = $this->teamtailorVerifier($domain, $content, $methodStr, $websiteType);
    if($stepOne !== false && is_array($stepOne))
      return $stepOne;

    $methodStr = $websiteType = '';
    $methodStr = 'No direct association with teamtailor';
    $websiteType = 'orignal company';
    $stepTwo = $this->linkCrawlerJobs($content, $methodStr, $websiteType, true);
    if($stepTwo['status'] != false)
      return $stepTwo;

    $content = '';
    if($stepTwo['status'] == false && isset($stepTwo['megaReturn']) && count($stepTwo['megaReturn']) > 0){
      $methodStr = 'No direct association with teamtailor upto 2 level deep';
      $websiteType = 'orignal company';
      foreach ($stepTwo['megaReturn'] as $pageLevel2 => $content) {
        $stepThree = $this->linkCrawlerJobs($content, $methodStr, $websiteType, $level2);
        if($stepThree['status'] != false)
          return $stepThree;
      }
    }

    return ['status' => false, 'tested' => 1, 'verified' => 0, 'links_checked' => json_encode($this->globaLink)];
  }


  public function getJobPageDetails($baseLink, $link){
    $content = $this->getStatusAndContent($baseLink.$link, "curl");
    if($content === false)
      return false;

    $filters = $this->jobPageFilters($content);
    //echo "<pre>",print_r($filters),"</pre>";
    $jobDetails = $this->jobDetails($content, $baseLink);
    //echo "<pre>",print_r($jobDetails),"</pre>";
    $return = array_merge($filters, $jobDetails);
    if($jobDetails['job_count'] == 0 && !isset($jobDetails['jobs'])){
      return $return;
    }
    $jobs = $jobDetails['jobs'];
    preg_match(config('teamtailor.patterns.domain'), $content['info']['url'], $domainMatch);
    $protocol = "http://";
    if(isset($domainMatch['protocol']) && !empty($domainMatch['protocol']))
      $protocol = $domainMatch['protocol'];
    $base = $protocol.$domainMatch['domain'];
    foreach ($jobs as $k => $job) {
      preg_match(config('teamtailor.patterns.full_url'), $job['link'], $fullMatch);
      if(isset($fullMatch['full_url']))
        $contentJob = $this->getStatusAndContent($job['link'], "curl");
      else
        $contentJob = $this->getStatusAndContent($base.$job['link'], "curl");
      if($contentJob === false){
        $jobs[$k]['contact_person'] = "not found";
        $jobs[$k]['contact_email'] = "not found";
        $jobs[$k]['contact_tel'] = "not found";
        continue;
      }
      $contactPerson = $this->evaluate($contentJob['content'], '//div[contains(concat(" ", normalize-space(@class), " "),"recruiter")]/a/@href');
      $contactPersonName = $this->evaluate($contentJob['content'], '//div[contains(concat(" ", normalize-space(@class), " "),"name-and-title")]/div[contains(concat(" ", normalize-space(@class), " "),"name")]/text()');
      if($contactPersonName->length == 1){
        $contactPersonName = $contactPersonName->item(0)->textContent;
      }
      if($contactPerson->length <= 0){
        $jobs[$k]['contact_person'] = "not found";
        $jobs[$k]['contact_email'] = "not found";
        $jobs[$k]['contact_tel'] = "not found";
        continue;
      }

      $cPPageLink = $base.$contactPerson->item(0)->textContent;
      $cPPage = $this->getStatusAndContent($cPPageLink, "curl");
      if($cPPage === false){
        $jobs[$k]['contact_person'] = "not found";
        $jobs[$k]['contact_email'] = "not found";
        $jobs[$k]['contact_tel'] = "not found";
        continue;
      }

      $cPName = $this->evaluate($cPPage['content'], '//*[contains(concat(" ", normalize-space(@class), " "),"info")]/h1');
      $cPEmail = $this->evaluate($cPPage['content'], '//*[contains(concat(" ", normalize-space(@class), " "),"contact-info")]/p/a[contains(concat(" ", normalize-space(@href), " "),"mailto:")]');
      $cPTel = $this->evaluate($cPPage['content'], '//*[contains(concat(" ", normalize-space(@class), " "),"contact-info")]/p/a[contains(concat(" ", normalize-space(@href), " "),"tel:")]');
      if($cPName->length <= 0){
        $jobs[$k]['contact_person'] = "not found";
      }else{
        $jobs[$k]['contact_person'] = $cPName->item(0)->textContent;
      }
      if($cPEmail->length <= 0){
        $jobs[$k]['contact_email'] = "not found";
      }else{
        $jobs[$k]['contact_email'] = $cPEmail->item(0)->textContent;
      }

      if($cPTel->length <= 0){
        $jobs[$k]['contact_tel'] = "not found";
      }else{
        $jobs[$k]['contact_tel'] = $cPTel->item(0)->textContent;
      }
    }
    $return['jobs'] = $jobs;
    return $return;
  }

  private function jobPageFilters($content){
    $return = ['department_filter' => 0, 'location_filter' => 0];
    $xpath = $this->evaluate($content['content'], '//div[contains(concat(" ", normalize-space(@class), " "),"jobs-filter-inner")]/div[contains(concat(" ", normalize-space(@class), " "),"dropdown")]');
    if($xpath->length == 0)
      return $return;

    foreach ($xpath as $filter) {
      if(in_array($filter->getAttribute('data-name'), config('teamtailor.keywords.departmentFilters')))
        $return['department_filter'] = 1;

      if(in_array($filter->getAttribute('data-name'), config('teamtailor.keywords.locationFilters')))
        $return['location_filter'] = 1;
    }
    return $return;
  }

  private function jobDetails($content, $base){
    $return = ['job_count' => 0];
    $xpath = $this->evaluate($content['content'], '//*[contains(concat(" ", normalize-space(@id), " "),"section-jobs")]/descendant::div[contains(concat(" ", normalize-space(@class), " "),"job-listing-container")]/ul');
    if($xpath->length == 0)
      return $return;
      foreach ($xpath as $jobs) {
        preg_match(config('teamtailor.patterns.jobCount'), $xpath->item(0)->getAttribute('class'), $jobCountMatch);
        if(isset($jobCountMatch['jobs']))
          $return['job_count'] = $jobCountMatch['jobs'];
        $jobs_links = $this->evaluate($content['content'], '//*[contains(concat(" ", normalize-space(@id), " "),"section-jobs")]/descendant::div[contains(concat(" ", normalize-space(@class), " "),"job-listing-container")]/ul/li/a/@href');
        $jobs_title = $this->evaluate($content['content'], '//*[contains(concat(" ", normalize-space(@id), " "),"section-jobs")]/descendant::div[contains(concat(" ", normalize-space(@class), " "),"job-listing-container")]/ul/li/descendant::*[contains(concat(" ", normalize-space(@class), " "),"title")]/text()');
        foreach ($jobs_links as $link) {
          $return['jobs']['link'][] = $base.$link->textContent;
          $return['jobs']['link_hash'][] = md5($base.$link->textContent);
        }

        foreach ($jobs_title as $title) {
          $return['jobs']['title'][] = $title->textContent;
        }

        $return['jobs'] = array_map(function($x, $y, $z) use($return){
          return ['link_hash' => $z, 'link' => $x, 'title' => $y];
        }, $return['jobs']['link'], $return['jobs']['title'], $return['jobs']['link_hash']);
      }
      return $return;
  }

  private function jobCrawler(){

  }


  private function generateHtml(\DOMElement $element){
      $innerHTML = "";
      foreach ($element->childNodes as $child){
          $innerHTML .= $element->ownerDocument->saveHTML($child);
      }
      return $innerHTML;
    }



  private function teamtailorVerifier($domain, $content, $methodStr, $websiteType){
    $returnIt = [];
    if(is_string($content['content']) && $this->findTextInDocument($content['content'], 'p', 'teamtailor', config('teamtailor.keywords.isTt'))){
      $url = $this->findTtJobPage($content['content'], 'a', ['logo', 'hidden-background']);
      similar_text($url, $content['info']['url'], $pcent);
      if($pcent >= 92.0){
        $parentDomain = $this->getParentDomain($content['content']);
        $jobPage = $this->verifyJobPage($content['content'], config('teamtailor.keywords.jobPage'));
        $returnIt = [
          'status' => true,
          'parent_domain' => $parentDomain,
          'method' => $methodStr,
          'redirects' => $content['info']['redirect_count'],
          'redirected_from' => $domain,
          'redirected_url' => $content['info']['url'],
          'job_url' => $url,
          'secure' => ($content['info']['primary_port'] == 443?1:0),
          'verified' => 1,
          'job_page' => $url.$jobPage,
          'type' => $websiteType,
          'links_checked' => (count($this->globaLink) <= 0)?json_encode([$domain, $url, $url.$jobPage]):json_encode($this->globaLink),
          'tested' => 1
        ];
        if(in_array($returnIt['parent_domain'], config('teamtailor.keywords.templateSite')) && $returnIt['job_url'] == $returnIt['job_page'])
          return false;
        else
          return array_merge($returnIt, $this->getJobPageDetails($url, $jobPage));

          // TODO: department_filter location_filter and job count, get all jobs too
          //$this->getJobPageDetails($url.$jobPage);
      }
    }
    return false;
  }


  private function linkCrawlerJobs($content, $methodStr, $websiteType, $others){
    $links = $this->getAllUrls($content['content']);
    //echo "<pre>",print_r($links),"</pre>";
    $baseUrl = $content['info']['url'];
    $megaReturn = null;
    if(isset($links['jobs'])){
      $pLinks = $this->prepareLink($links['jobs'], $baseUrl);
      if($pLinks !== false){
        foreach ($pLinks as $page) {
          $resultPage = $this->processLinks($page, $methodStr, $websiteType);
          if($resultPage['status'] !== false)
            return $resultPage;
          if(!$resultPage['status'] && $resultPage['content'] !== false)
            $megaReturn[$page] = $resultPage['content'];

          $this->globaLink[] = $page;
        }
        return ['status' => false, 'megaReturn' => $megaReturn];
      }
    }
    $pLinks = []; $page = $resultPage = '';
    if(isset($links['others']) && $others){
      $pLinks = $this->prepareLink($links['others'], $baseUrl);
      if($pLinks !== false){
        foreach ($pLinks as $page) {
          $resultPage = $this->processLinks($page, $methodStr, $websiteType);
          if($resultPage['status'] !== false)
            return $resultPage;

          if(!$resultPage['status'] && $resultPage['content'] !== false)
            $megaReturn[$page] = $resultPage['content'];

          $this->globaLink[] = $page;
        }
      }
    }

    return ['status' => false, 'megaReturn' => $megaReturn];
  }



  private function processLinks($links, $methodStr, $websiteType, $type = 'curl'){
      $content = $this->getStatusAndContent($links, $type);
      if(is_bool($content) && $content === false)
        return ['status' => false, 'content' => $content];

      //setting charset
      $this->setCharset($content['info']['content_type']);
      $verified = $this->teamtailorVerifier($links, $content, $methodStr, $websiteType);
      if($verified !== false && is_array($verified))
        return $verified;
    return ['status' => false, 'content' => $content];
  }



  private function prepareLink($links, $base){
    $retLinks = false;
    foreach ($links as $link) {
      $postLnk = $retLinksTemp = '';

      //if link is relative
      preg_match_all(config('teamtailor.patterns.relative_link'), $link, $resultRelative);
      if(!empty($resultRelative['relative_link'])){
        preg_match_all(config('teamtailor.patterns.domain'), $base, $baseAry);
        if(empty($baseAry['domain'][0]))
          continue;
        if(empty($baseAry['protocol'][0]))
          $protoMix = 'http://';
        $protoMix = $baseAry['protocol'][0];
        $postLnk = $resultRelative['relative_link'][0];
        if(in_array($protoMix.$baseAry['domain'][0].'/'.$postLnk, $this->globaLink))
          continue;
        $retLinksTemp = $protoMix.$baseAry['domain'][0].'/'.$postLnk;
      }

      //if link is full URI
      preg_match_all(config('teamtailor.patterns.full_url'), $link, $resultFull);
      if(!empty($resultFull['full_url'])){
        // if($this->compareBaseDomain($base, $link)){
          if(in_array($link, $this->globaLink)){
            continue;
          }
          $retLinksTemp = $link;
        // }
      }

      //exclude known bad links or links which does not yeild results
      foreach (config('teamtailor.keywords.exclude') as $exclude) {
        $excludePatt = str_replace("##", $exclude, config('teamtailor.patterns.pattTemp'));
        preg_match_all(config('teamtailor.patterns.domain'), $retLinksTemp, $finalMatch);
        if(empty($finalMatch['resource'][0])){
          continue;
        }
        preg_match_all($excludePatt, $finalMatch['resource'][0], $excludeMatch);
        if(!empty($excludeMatch['match'][0]))
          continue 2;
      }

      //exclude known service providers like google, facebook youtube etc
      foreach (config('teamtailor.keywords.excludeKnown') as $excludeKnown) {
        $excludeKnownPatt = str_replace("##", $excludeKnown, config('teamtailor.patterns.pattTemp'));
        preg_match_all($excludeKnownPatt, $retLinksTemp, $excludeKnownMatch);
        if(!empty($excludeKnownMatch['match'][0]))
          continue 2;
      }
      //combine in one array
      $retLinks[] = $retLinksTemp;
    }
    return $retLinks;
  }


  private function getAllUrls($content){
    $xpath = $this->evaluate($content, '//body/descendant::a[contains(concat(" ", normalize-space(@href), " "),"")]');
    if($xpath->length == 0 )
      return false;
      $links = ['jobs' => [], 'others' => []];
      foreach ($xpath as $nodeKey => $node) {

        foreach (config('teamtailor.keywords.swed') as $swedKeys) {
          $swedPatt = str_replace("##", $swedKeys, config('teamtailor.patterns.pattTemp'));
          preg_match_all($swedPatt, $node->textContent, $swedMatch);
          if(!empty($swedMatch['match'][0])){
            $links['jobs'][] = $node->getAttribute('href');
            continue 2;
          }
        }

        foreach (config('teamtailor.keywords.eng') as $engKeys) {
          $engPatt = str_replace("##", $engKeys, config('teamtailor.patterns.pattTemp'));
          preg_match_all($engPatt, $node->textContent, $engMatch);
          if(!empty($engMatch['match'][0])){
            $links['jobs'][] = $node->getAttribute('href');
            continue 2;
          }
        }
        $links['others'][] = $node->getAttribute('href');
      }

      if(count($links['jobs']) > 1)
        $links['jobs'] = array_unique($links['jobs']);

      if(count($links['others']) > 1)
        $links['others'] = array_unique($links['others']);

      return $links;
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
        if(in_array(str_replace('/', '', mb_strtolower(trim($node_main->textContent))), $match))
          return $node_main->textContent;
      }
    }

    $xpath = $this->evaluate($content, '//descendant::a[contains(concat(" ", normalize-space(@href), " "),"")]/@href');
    if($xpath->length == 0){
      return false;
    }

    foreach ($xpath as $key => $node) {
      if(in_array(str_replace('/', '', mb_strtolower(trim($node->textContent))), $match))
        return $node->textContent;
    }
  }




  private function findTextInDocument($content, $parent, $search, $match){
    $search = $this->getUniqueLowerUpperCase($search);
    $xpath = $this->evaluate($content, '//'.$parent.'[text()[contains(translate(., "'.$search['upper'].'", "'.$search['lower'].'"), "'.$search['search'].'")]]');
    if($xpath->length == 0){
      $xpath_script = $this->evaluate($content, '//script[text()[contains(concat(" ", normalize-space(.), " "),"'.$search['search'].'")]]');
      $xpath_style = $this->evaluate($content, '//style[text()[contains(concat(" ", normalize-space(.), " "),"'.$search['search'].'")]]');
      $xpath_links = $this->evaluate($content, '//*[contains(concat(" ", normalize-space(@href), " "),"'.$search['search'].'")]');
      if($xpath_links->length > 0 && ($xpath_style->length > 0 || $xpath_script->length > 0))
        return true;
      return false;
    }

    foreach ($xpath as $key => $node) {
      if(in_array(mb_strtolower(trim($node->textContent)), $match))
        return true;
    }
    return false;
  }


  private function getParentDomain($content){
    $xpath = $this->evaluate($content, '//*[contains(concat(" ", normalize-space(@class), " "),"about-inner__numbers")]/descendant::a[contains(concat(" ", normalize-space(@class), " "),"website")]');
    if($xpath->length == 1){
      return $xpath->item(0)->textContent;
    }
    return 'unknown';
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
    try {
      $DOM->loadHTML($content);
    } catch (\Exception $e) {

    }
    libxml_use_internal_errors($internalErrors);
    $xpath = new \DOMXPath($DOM);
    return $xpath->evaluate($expression);
  }


  private function compareBaseDomain($base, $new){
    preg_match_all(config('teamtailor.patterns.domain'), $new, $doCheck);
    preg_match_all(config('teamtailor.patterns.domain'), $base, $doBase);
    $manager = new Manager(new Cache(), new CurlHttpClient());
    $rules = $manager->getRules();
    if(empty($doCheck['domain'][0]))
      return false;
    if(empty($doBase['domain'][0]))
      return false;
    $domainNew = $rules->resolve($doCheck['domain'][0]);
    $domainBase = $rules->resolve($doBase['domain'][0]);
    if($domainNew->getRegistrableDomain() != $domainBase->getRegistrableDomain())
      return false;

    return true;
  }

  public function getSiteContents($domain, $baseDomain = null){
    //checking url validity
    $manager = new Manager(new Cache(), new CurlHttpClient());
    $rules = $manager->getRules();
    preg_match_all(config('teamtailor.patterns.domain'), $domain, $doCheck);
    if(empty($doCheck['domain'][0]))
      return false;
    $domainObj = $rules->resolve($doCheck['domain'][0]);
    if(!$domainObj->isKnown() && !$domainObj->isICANN() && !$domainObj->isResolvable())
      return false;

    //base domain check (eleminate all links which is not under same domain as parent)
    // if($baseDomain !== null && $domainObj->getRegistrableDomain() != $baseDomain)
      // return false;

    if(empty($doCheck['protocol'][0])){
      $domain = 'http://'.$domain;
    }

    //cURL starts
    $curl = new Curl();
    $curl->setUserAgent('TechkumarJobsBot /0.1.2 (+http://projects.techkumar.in/bots/job)');
    $curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
    $curl->setOpt(CURLOPT_MAXREDIRS, 7);
    $curl->get($domain);
    //echo "<pre>",print_r($curl->getRawResponseHeaders()),"</pre>";

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
    set_time_limit(120);
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
    return ['upper' => mb_strtoupper(count_chars($in, 3)), 'lower' => mb_strtolower(count_chars($in, 3)), 'search' => $in];
  }


  private function setCharset($contentType){
    preg_match('/charset=([^()<>@,;:\"\/[\]?.=\s]*)/', $contentType, $charset);
    $charset = isset($charset[1]) && is_string($charset[1])?in_array(mb_strtoupper($charset[1]), mb_list_encodings())?mb_strtoupper($charset[1]):false:false;
    if($charset !== false)
      mb_internal_encoding($charset);
  }
}
