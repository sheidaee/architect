<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller {

    public function index(){
        return Template::all();
	}

}
