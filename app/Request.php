<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Request extends Model {

	protected $table = "requests";

	protected $fillable = ['start_time', 'end_time', 'place', 'description', 'category_id', 'author_id'];

	public function author()
	{
		return $this->belongsTo('App\User', 'author_id');
	}

}
