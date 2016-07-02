<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\RequestCategory;
use Illuminate\Support\Facades\Response;

class RequestCategoriesController extends Controller
{
    public function index()
    {
        $categories = RequestCategory::all();
        return Response::json(compact('categories'));
    }
}
