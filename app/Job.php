<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
  protected $fillable = ['title', 'title_full', 'department', 'location', 'contact_person', 'contact_email'];

  public function jobable(){
      return $this->morphTo();
  }
}
