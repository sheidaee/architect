<?php namespace App\Http\Controllers;

use App\Folder;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FolderController extends Controller {

    public function index(){
        return Folder::all();
	}

    public function store(Request $request){

        $folder = Folder::create(['name'=>$request->get('name')]);

        return new Response($folder, 201);
    }

    public function delete($id){
        $folder = Folder::find($id);

        if($folder->delete()) {
            return Response(1, 200);
        }
        return new Response(0, 400);
    }

}
