<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Auth;
use Hash;
use Session;
use App\User;

class HomeController extends Controller{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(){
        return view('admin.dashboard');
    }


    public function profile(){
      return view('admin.profile.index');
    }

    public function changePassword(Request $request){
       if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
           // The passwords matches
           Session::flash('alert', 'alert-error');
           return redirect()->back()->with("message","Your current password does not matches with the password you provided. Please try again.");
       }
       if(strcmp($request->get('current-password'), $request->get('new-password')) == 0){
           //Current password and new password are same
           Session::flash('alert', 'alert-error');
           return redirect()->back()->with("message","New Password cannot be same as your current password. Please choose a different password.");
       }
       $validatedData = $request->validate([
           'current-password' => 'required',
           'new-password' => 'required|string|min:6|confirmed',
       ]);
       //Change Password
       $user = Auth::user();
       $user->password = bcrypt($request->get('new-password'));
       $user->save();
       Auth::logout();
       Session::flash('alert', 'alert-success');
       return redirect()->back()->with("message","Password changed successfully !");
   }
}
