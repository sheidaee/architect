<?php namespace App\Http\Controllers;


use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Requests\LibraryRequest;
use App\Library;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LibraryController extends Controller {

    public function index()
    {
        return Library::all();
    }

    public function detach($page, $library)
    {
        $lib = Library::query()->where('name', '=', $library)->with('pages')->first();

        if ($lib->pages()->get())
        {
            $lib->pages()->detach($page);

            return $lib;
        }

        return new Response(trans('validation.cantFindLibrary'), 400);
    }

    public function attach($page, $library){
        $lib = Library::query()->where('name', '=', $library)->first();

        if ($lib) {
            $lib->pages()->attach($page);

            return $lib;
        }

        return new Response(trans('validation.cantFindLibrary'), 400);
    }

    public function store(LibraryRequest $request){

        if($library = Library::create($request->all()))
            return $library;

        return 'noo';
//        return new Response(trans('validation.cantInsertLibrary'), 400);
    }

    public function update(LibraryRequest $request, $id){
        $library = Library::find($id);

        if($library) {
            //$library->fill($library);
//            $library->save();
            $library->update($request->all());
            return $library;
//            $library->update($request->all());

        }
        return new Response('error', 400);

    }

    public function delete($id){
        $library = Library::find($id);

        if($library) {
            return  new Response(1, 200);
        }
        return new Response('libraryDeleteFail', 500);
    }

    public function delete_test($id){
    $library = Library::find($id);

    if($library) {
        return  new Response(1, 200);
    }
    return new Response('libraryDeleteFail', 500);
}

    public function update2(Library $library, $id, $name, $path){
        $isLibrary = Library::find($id);

        if($isLibrary) {
            return $isLibrary;
//            $library->fill($library);
//            $library->save();
            $library->update(['name'=>$name, 'path'=>$path]);

            return $library;
        }
        return new Response('error', 400);

    }
}
