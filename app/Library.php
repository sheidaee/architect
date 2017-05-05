<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class Library extends Model {

    protected $fillable = array('name', 'path', 'type', 'user_id');

    function setUserIdAttribute() {
        $this->attributes['user_id'] = Auth::user()->id;
    }

    public function pages()
    {
        return $this->belongsToMany('App\Page', 'pages_libraries', 'library_id', 'page_id');
    }
}
