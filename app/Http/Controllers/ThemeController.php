<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Theme;
use Illuminate\Http\Request;

class ThemeController extends Controller {

    public function index(){
        return Theme::all();
	}

}
