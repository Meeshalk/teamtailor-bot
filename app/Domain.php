<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
  protected $fillable = ['domain', 'orignal_url', 'method', 'redirects', 'redirected_from', 'redirected_url', 'job_url', 'secure', 'verified',
                         'job_page', 'type', 'tested', 'links_checked', 'completed_in', 'department_filter', 'location_filter', 'job_count'];

  public function domainable(){
      return $this->morphTo();
  }

  //one to many polymorphic
  public function jobs(){
      return $this->morphMany('App\Job', 'jobable');
  }
}
