<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestCategory extends Model {

	protected $table = "request_categories";

	public function requests()
	{
		return $this->hasMany('App\Request');
	}

}
