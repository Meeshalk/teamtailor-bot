<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
  protected $fillable = ['link_hash', 'title', 'link', 'contact_person', 'contact_email', 'contact_tel'];

  public function jobable(){
      return $this->morphTo();
  }
}
