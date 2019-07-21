<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Seed extends Model
{
  protected $fillable = ['name', 'file', 'note', 'header'];

    //one to many polymorphic
    public function domains(){
        return $this->morphMany('App\Domain', 'domainable');
    }
}
