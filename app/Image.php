<?php namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;

class Image extends Model {

    protected $fillable = [
        'file_name',
        'user_id',
        'display_name'
    ];

    function setUserIdAttribute() {
        $this->attributes['user_id'] = Auth::user()->id;
    }

    public function folders()
    {
        //, 'images_folders', 'image_id', 'folder_id'
        return $this->belongsToMany('App\Folder', 'images_folders', 'image_id', 'folder_id');
    }
}
