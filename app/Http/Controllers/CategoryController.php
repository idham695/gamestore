<?php

namespace App\Http\Controllers;

use App\Model\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller {
    public function index() {

    
        $category = Category::OrderBy("id", "DESC")->paginate(2)->toArray();

        $output = [
            "message" => "category",
            "result" => $category
        ];

        return response()->json($category, 200);
    }

    public function store(Request $request){
        
        $input = $request->all();
         if(Gate::denies('admin')){
            return response()->json([
                'success' => false,
                'status'=>403,
                'message' => 'You are unauthorized'

            ],403);
        }
        $this->validate($request, [
            'nama' => 'required',
        ]);

        $category = Category::create($input);
        return response()->json($category, 200);
    }

    public function show($id){
        $category = Category::find($id);

        if(!$category){
            abort(404);
        }
       

        return response()->json($category, 200);
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();

        $category = Category::find($id);

        if (!$category) {
            abort(404);
        }
        if(Gate::denies('admin')){
            return response()->json([
                'success' => false,
                'status'=>403,
                'message' => 'You are unauthorized'

            ],403);
        }

        $this->validate($request, [
            'nama' => 'required',
        ]);

        $category->fill($input);
        $category->save();

        return response()->json($category, 200);
    }
    
    public function destroy($id)
    {
        $category = Category::find($id);

        if(!$category){
            abort(404);
        }
        if(Gate::denies('admin')){
            return response()->json([
                'success' => false,
                'status'=>403,
                'message' => 'You are unauthorized'

            ],403);
        }

        $category->delete();
        $message = ['message' => 'deleted successfully', 'category_id' => $id];

        return response()->json($message, 200);
    }
}