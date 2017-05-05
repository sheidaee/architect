<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends Model {

//    protected $fillable = ['id', 'pageable_id', 'pageable_type', 'created_at', 'updated_at'];
        protected $fillable = ['name', 'html', 'css', 'js', 'theme', 'description', 'tags', 'title', 'libraries'];
//    public function pageable()
//    {
//        return $this->morphTo();
//    }

    public function libraries()
    {
        return $this->belongsToMany('App\Library', 'pages_libraries', 'page_id', 'library_id');
    }

    public function pageable()
    {
        return $this->morphTo();
    }

}
