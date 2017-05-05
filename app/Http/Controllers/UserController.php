<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\User;
use Illuminate\Http\Request;
use Auth;

class UserController extends Controller {

    public function projects(){
        $user = Auth::user();
        return $user->projects;
    }

    public function project($name){
        $user = Auth::user();
        return new Response($user->projects()->with('pages.libraries')->where('name', '=', $name )->get()[0], 200);
    }


//    public function articles() {
//        $user = User::first();
//        $articles = $user->articles;
//        return view('article/index', compact('articles'));
//    }

}
