<?php

namespace App\Http\Controllers;

use App\Model\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller {
    public function index() {
        $product = Product::with('category')->OrderBy("id", "DESC")->paginate(10);

        $output = [
            "message" => "product",
            "result" => $product
        ];

        return response()->json($product, 200);
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
            'no_seri' => 'required|unique:product',
            'nama_produk' => 'required',
            'id_kategori' => ' required|exists:kategori,id',
            'harga' => 'required',
            'photo_produk' => 'required',
            'deskripsi' => ' required',
        ]);
        $product = new Product();
        $product->no_seri = $request->input('no_seri');
        $product->nama_produk = $request->input('nama_produk');
        $product->id_kategori = $request->input('id_kategori');
        $product->harga = $request->input('harga');
        $product->deskripsi = $request->input('deskripsi');

        if ($request->hasFile('photo_produk')) {
            $firstName = str_replace(' ','_', $request->input('no_seri'));
            $lastName = str_replace(' ','_', $request->input('nama_produk'));

            $imgName = $firstName . '_' . $lastName;
            $request->file('photo_produk')->move(storage_path('uploads/image_produk'), $imgName);

            $current_image_path = storage_path('avatar') . '/' . $product->photo_produk;
            if (file_exists($current_image_path)) {
                unlink($current_image_path);
            }

            $product->photo_produk = $imgName;
        }
        $product->save();
        return response()->json($product, 200);
    }
    public function getImage($imageName){
        $imagePath = storage_path('uploads/image_produk') . '/' . $imageName;
        if (file_exists($imagePath)) {
            $file = file_get_contents($imagePath);
            return response($file, 200)->header('Content-Type', 'image/jpeg');
        }
        return response()->json(array(
            "message" => "Image not found"
        ), 401);
    }

    public function show($id){
        $product = Product::with('category')->find($id);

        if(!$product){
            abort(404);
        }
        
        return response()->json($product, 200);
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        if(Gate::denies('admin')){
            return response()->json([
                'success' => false,
                'status'=>403,
                'message' => 'You are unauthorized'

            ],403);
        }
        $product = Product::find($id);

        if (!$product) {
            abort(404);
        }

        $this->validate($request, [
            'no_seri' => 'required|exists:product,no_seri',
            'nama_produk' => 'required',
            'id_kategori' => ' required|exists:kategori,id',
            'harga' => 'required',
            'photo_produk' => 'required',
            'deskripsi' => ' required',
        ]);

        $product->no_seri = $request->input('no_seri');
        $product->nama_produk = $request->input('nama_produk');
        $product->id_kategori = $request->input('id_kategori');
        $product->harga = $request->input('harga');
        $product->deskripsi = $request->input('deskripsi');

        if ($request->hasFile('photo_produk')) {
            $firstName = str_replace(' ','_', $request->input('no_seri'));
            $lastName = str_replace(' ','_', $request->input('nama_produk'));

            $imgName = $firstName . '_' . $lastName;
            $request->file('photo')->move(storage_path('uploads/image_produk'), $imgName);

            $current_image_path = storage_path('avatar') . '/' . $product->photo_produk;
            if (file_exists($current_image_path)) {
                unlink($current_image_path);
            }

            $product->photo = $imgName;
        }
        $product->save();

        return response()->json($product, 200);
    }
    
    public function destroy($id)
    {
        $product = Product::find($id);

        if(!$product){
            abort(404);
        }
        if(Gate::denies('admin')){
            return response()->json([
                'success' => false,
                'status'=>403,
                'message' => 'You are unauthorized'

            ],403);
        }

        $product->delete();
        $message = ['message' => 'deleted successfully', 'product_id' => $id];

        return response()->json($message, 200);
    }
}