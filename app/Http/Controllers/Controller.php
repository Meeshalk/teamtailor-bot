<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public static function sentenceCase($string) {
      $sentences = preg_split('/([.?!]+)/', $string, -1,PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
      $newString = '';
      foreach ($sentences as $key => $sentence) {
          $newString .= ($key & 1) == 0?
              ucfirst(strtolower(trim($sentence))) :
              $sentence.' ';
      }
      return trim($newString);
    }
}
