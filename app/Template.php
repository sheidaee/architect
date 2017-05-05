<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Template extends Model {

    protected $morphClass = 'Template';

    public function pages(){
        return $this->morphMany('App\Page', 'pageable');
//        return $this->hasMany('App\Page', 'pageable_id');
    }
}
