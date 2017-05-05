<?php namespace App\Http\Controllers;


use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Requests\ImageRequest;
use App\Image;
use File;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

//use Symfony\Component\HttpFoundation\File\UploadedFile as file;

class ImageController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return Image::with('folders')->get();
    }

    public function store(Request $request)
    {
        //replace old image modified by aviary api
        if ($request->get('url'))
        {
            $dir = asset('images/uploads/');
            if ($request->get('oldUrl'))
            {
                $arr = explode('/', $request->get('oldUrl'));
                $path = $dir . end($arr);
            } else
            {
                $path = $dir . str_random(10) . '.' . pathinfo($request->get('url'), PATHINFO_EXTENSION);
            }

            file_put_contents($path, file_get_contents($request->get('url')));

            return str_replace($dir, asset('images/uploads/'), $path);
        }
        $imgs = array();
        $userId = Auth::user()->id;
        $files = $request->files;

        foreach($files as $file) {
            //compile names
            $name = pathinfo($file->getClientOriginalName());

            $rand = str_random(15).'.'.$name['extension'];

            //if display name already exists add a random number to it
            if (Image::where('user_id', $userId)->where('display_name', $name['basename'])->first())
            {
                $name['basename'] = str_replace('.', rand(100, 999).'.', $name['basename']);
            }

            //save the image
            $file->move('images/uploads', $rand);
//            return new Response(public_path(), 200);

            //save reference to image in db
            $img = Image::create(array(
                'file_name' => $rand,
                'user_id' => $userId,
                'display_name' => $name['basename']
            ));

            //attach to folder
            if ($request->get('folderId')) {
                $img->folders()->attach($request->get('folderId'));
            }

            $img->folder = $request->get('folderName');
            $imgs[] = $img->toArray();

        }

        return new Response(json_encode($imgs), 201);

    }

    public function delete($id){
        $img = Image::find($id);

        File::delete('images/uploads/'.$img->file_name);

        if ($img->delete($id))
            return new Response(1, 200);
        return new Response(0, 500);
    }

    public function deleteMultiple(Request $request){
        if ($request->has('ids')) {
            foreach ($request->get('ids') as $id) {
                if ($img = Image::find($id)) {
                    File::delete('images/uploads/'.$img->file_name);
                    $img->delete($id);
                }
            }
        }

        return new Response(json_encode($request->get('ids')), 200);
    }

    public function store_test(ImageRequest $request, $id, $folder)
    {
        $img = Image::find($id);

        $img->folders()->attach($folder);
        return $img->with('folders')->get();
        //replace old image modified by aviary api
//        if ($request->get('url'))
//        {
//            return new Response(json_encode($request->get('url')), 200);
//            $dir = asset('images/uploads/');
//            if ($request->get('oldUrl'))
//            {
//                $arr = explode('/', $request->get('oldUrl'));
//                $path = $dir . end($arr);
//            } else
//            {
//                $path = $dir . str_random(10) . '.' . pathinfo($request->get('url'), PATHINFO_EXTENSION);
//            }
//
//            file_put_contents($path, file_get_contents($request->get('url')));
//
//            return str_replace($dir, asset('images/uploads/'), $path);
//        }
//        $imgs = array();
//        $userId = Auth::user()->id;
//        $files = $request->file();
//        return new Response(json_encode('ok'), 200);
//        foreach($files as $file) {
//            return new Response(json_encode($file), 200);
//            //compile names
////            \File::move();
//            $name = pathinfo($file->getClientOriginalName());
//            $rand = str_random(15).'.'.$name['extension'];
//
//            //if display name already exists add a random number to it
//            if (Image::where('user_id', $userId)->where('display_name', $name['basename'])->first())
//            {
//                $name['basename'] = str_replace('.', rand(100, 999).'.', $name['basename']);
//            }
//
//            //save the image
//            $file->move(asset('images/uploads'), $rand);
//
//            //save reference to image in db
//            $img = Image::create(array(
//                'file_name' => $rand,
//                'user_id' => $userId,
//                'display_name' => $name['basename']
//            ));
//
//            //attach to folder
//            if ($request->get('folderId')) {
//                $img->folders()->attach($request->get('folderId'));
//            }
//
//            $img->folder = $request->get('folderName');
//            $imgs[] = $img->toArray();
//
//        }

        return new Response(json_encode($imgs), 201);

    }
//	/**
//	 * Show the form for creating a new resource.
//	 *
//	 * @return Response
//	 */
//	public function create()
//	{
//		//
//	}
//
//	/**
//	 * Store a newly created resource in storage.
//	 *
//	 * @return Response
//	 */
//	public function store()
//	{
//		//
//	}
//
//	/**
//	 * Display the specified resource.
//	 *
//	 * @param  int  $id
//	 * @return Response
//	 */
//	public function show($id)
//	{
//		//
//	}
//
//	/**
//	 * Show the form for editing the specified resource.
//	 *
//	 * @param  int  $id
//	 * @return Response
//	 */
//	public function edit($id)
//	{
//		//
//	}
//
//	/**
//	 * Update the specified resource in storage.
//	 *
//	 * @param  int  $id
//	 * @return Response
//	 */
//	public function update($id)
//	{
//		//
//	}
//
//	/**
//	 * Remove the specified resource from storage.
//	 *
//	 * @param  int  $id
//	 * @return Response
//	 */
//	public function destroy($id)
//	{
//		//
//	}

}
