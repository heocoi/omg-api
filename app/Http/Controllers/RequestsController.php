<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Input;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RequestsController extends Controller
{

    function __construct()
    {
        $this->middleware('jwt.auth');
        $this->currentUser = JWTAuth::toUser(JWTAuth::getToken());
    }

    public function index()
    {
        $requests = Request::with('author')->get();
        return Response::json(compact('requests'), 200);
    }

    public function show($id)
    {
        try {
            $request = Request::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return Response::json(['error' => 'The request with ID: ' . $id . ' was not found.'], 404);
        }
        return Response::json(compact('request'), 200);
    }

    /**
     * store new request
     * @return
     */
    public function store()
    {
        $request = Request::create([
            'start_time' => Input::get('start_time'),
            'end_time' => Input::get('end_time'),
            'place' => Input::get('place'),
            'description' => Input::get('description'),
            'category_id' => Input::get('category_id'),
            'author_id' => $this->currentUser->id
        ]);

        return Response::json($request, 200);
    }

    public function update($id)
    {
        try {
            $request = Request::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return Response::json(['error' => 'The request with ID: ' . $id . ' was not found.'], 404);
        }

        if ($request->author_id != $this->currentUser->id) {
            return Response::json(['error' => 'Permission denied'], 403);
        }

        $request->category_id = Input::get('category_id');
        $request->start_time = Input::get('start_time');
        $request->end_time = Input::get('end_time');
        $request->place = Input::get('place');
        $request->description = Input::get('category_id');
        $request->save();

        return Response::json(compact('request'), 200);
    }
}
