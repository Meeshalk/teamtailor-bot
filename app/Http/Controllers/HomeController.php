<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Auth;
use App\User;

class HomeController extends Controller{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(){
      $user = User::find(Auth::user()->id);
      $role = null;
      foreach ($user->roles as $r) {
        $role = $r->name;
      }
        return view('admin.dashboard', ['role' => $role]);
    }
}
