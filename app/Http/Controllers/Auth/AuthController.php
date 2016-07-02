<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller {

	public function signup()
	{
		try {
			$user = User::create([
				'email' => Input::get('email'),
				'password' => bcrypt(Input::get('password'))
			]);
		} catch (Exception $e) {
			return Response::json(['error' => 'User already exists.'], 409);
		}
		$token = JWTAuth::fromUser($user);

		return Response::json(compact('token'));
	}

	public function login()
	{
		$credentials = Input::only('email', 'password');
		if (!$token = JWTAuth::attempt($credentials)) {
			return Response::json(false, 401);
		}

		return Response::json(compact('token'));
	}

	// XXX just clear token on client device when logout
	// public function logout() {}

}
