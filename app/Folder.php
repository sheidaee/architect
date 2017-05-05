<?php namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model {

    protected $fillable = ['name'];

    function setUserIdAttribute() {
        $this->attributes['user_id'] = Auth::user()->id;
    }
//, 'images_folders', 'image_id', 'folder_id'
    public function images()
    {
        return $this->belongsToMany('App\Image', 'images_folders', 'image_id', 'folder_id');
    }

}
