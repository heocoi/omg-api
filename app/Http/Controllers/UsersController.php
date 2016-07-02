<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class UsersController extends Controller
{

    function __construct()
    {
        $this->middleware('jwt.auth');
        $this->currentUser = JWTAuth::toUser(JWTAuth::getToken());
    }

    /**
     * show all users info (not include current user)
     * @return mixed
     */
    public function index()
    {
        $users = User::where('id', '<>', $this->currentUser->id)->get();
        return Response::json(compact('users'), 200);
    }

    /**
     * show user info
     * @param  $id
     * @return  mixed
     */
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return Response::json(['error' => 'User with ID: ' . $id . ' was not found.'], 404);
        }
        return Response::json(compact('user'), 200);
    }

    public function getRequestsByUser($id)
    {
        if ($this->currentUser->id != $id) {
            return Response::json(['error' => 'Permission denied.'], 403);
        }

        $requests = User::find($id)->requests;
        return Response::json(compact('requests'), 200);
    }
}
