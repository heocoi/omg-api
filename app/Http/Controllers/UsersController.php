<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Input;
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
            $requests = $user->requests;
        } catch (ModelNotFoundException $e) {
            return Response::json(['error' => 'User with ID: ' . $id . ' was not found.'], 404);
        }
        return Response::json(compact('user', 'requests'), 200);
    }

    public function getRequestsByUser($id)
    {
        if ($this->currentUser->id != $id) {
            return Response::json(['error' => 'Permission denied.'], 403);
        }

        $requests = User::find($id)->requests;
        $requests->load('author');
        return Response::json(compact('requests'), 200);
    }

    public function updateProfileByUser($id)
    {
        if ($this->currentUser->id != $id) {
            return Response::json(['error' => 'Permission denied.'], 403);
        }

        $user = User::find($id);
        if (Input::has('first_name')) $user->first_name = Input::get('first_name');
        if (Input::has('last_name')) $user->last_name = Input::get('last_name');
        if (Input::has('country')) $user->country = Input::get('country');
        if (Input::has('age')) $user->age = Input::get('age');
        if (Input::has('gender')) $user->gender = Input::get('gender');
        if (Input::has('type')) $user->type = Input::get('type');
        if (Input::has('language')) $user->language = Input::get('language');
        if (Input::has('introduction')) $user->introduction = Input::get('introduction');
        if (Input::has('latitude')) $user->latitude = Input::get('latitude');
        if (Input::has('longitude')) $user->longitude = Input::get('longitude');
        if (Input::has('is_sharing_location')) $user->is_sharing_location = Input::get('is_sharing_location');
        if (Input::has('receive_notifications')) $user->receive_notifications = Input::get('receive_notifications');
        $user->save();
        return Response::json(compact('user'), 200);
    }

    public function getSharingLocationUsers()
    {
        $users = User::where('is_sharing_location', true)->get();
        return Response::json(compact('users'), 200);
    }

}
