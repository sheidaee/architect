<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class Project extends Model {

	protected $fillable = [
        'name','user_id'
    ];

    protected $morphClass = 'Project';

    function setUserIdAttribute() {
        $this->attributes['user_id'] = Auth::user()->id;
        // $this->attributes['published_at'] = Carbon::parse($date);
    }

    /**
     * because 1 to n project is one user is many - refer
     * An project owned by a user (relationship)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo('App\User');
    }

//    public function pages()
//    {
//        return $this->morphMany('App\Page', 'pageable');
//    }

    public function pages(){
//        return 'ok';
//        return $this->hasMany('App\Page', 'pageable_id');
        return $this->morphMany('App\page', 'pageable');
    }

}
