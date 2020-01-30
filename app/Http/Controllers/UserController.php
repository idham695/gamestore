<?php

namespace App\Http\Controllers;

use App\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller {
    public function index() {
        $user = User::OrderBy("id", "DESC")->paginate(10);
        if(Gate::denies('admin')){
            return response()->json([
                'success' => false,
                'status'=>403,
                'message' => 'You are unauthorized'

            ],403);
        }
        $output = [
            "message" => "user",
            "result" => $user
        ];

        return response()->json($user, 200);
    }
    public function register(Request $request){
        $input = $request->all();
        $this->validate($request, [
            'nama' => 'required',
            'username' => 'required|email',
            'password' => ' required',
            'no_telp' => 'required',
            'role' => ' required|in:admin,pelanggan',
            'alamat' => 'required'
        ]);

        $user = new User;
        $user->nama = $request->input('nama');
        $user->username = $request->input('username');
        $plainPassword = $request->input('password');
        $user->password = app('hash')->make($plainPassword);
        $user->no_telp = $request->input('no_telp');

        if ($request->hasFile('photo')) {
            $firstName = str_replace(' ','_', $request->input('nama'));
            $lastName = str_replace(' ','_', $request->input('username'));

            $imgName = $firstName . '_' . $lastName;
            $request->file('photo')->move(storage_path('uploads/image_user'), $imgName);

            $current_image_path = storage_path('avatar') . '/' . $user->photo;
            if (file_exists($current_image_path)) {
                unlink($current_image_path);
            }

            $user->photo = $imgName;
        }
        $user->role = $request->input('role');
        $user->alamat = $request->input('alamat');
        $user->save();        
        return response()->json($user, 200);
    }
    public function login(Request $request){
        $input = $request->all();

        $this->validate($request, [
            'username' => 'required',
            'password' => ' required',
        ]);
        $credentials = $request->only(['username', 'password']);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60 * 24
        ], 200);

    }

    public function getImage($imageName){
        $imagePath = storage_path('uploads/image_user') . '/' . $imageName;
        if (file_exists($imagePath)) {
            $file = file_get_contents($imagePath);
            return response($file, 200)->header('Content-Type', 'image/jpeg');
        }
        return response()->json(array(
            "message" => "Image not found"
        ), 401);
    }

    public function show($id){
        $user = User::find($id);

        if(!$user){
            abort(404);
        }
        if(Gate::denies('admin')){
            return response()->json([
                'success' => false,
                'status'=>403,
                'message' => 'You are unauthorized'

            ],403);
        }

        return response()->json($user, 200);
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();

        $user = User::find($id);

        if (!$user) {
            abort(404);
        }

        $this->validate($request, [
            'nama' => 'required',
            'username' => 'required',
            'password' => ' required',
            'no_telp' => 'required',
            'photo' => 'required',
            'role' => ' required|in:admin,pelanggan',
            'alamat' => 'required'
        ]);
        $user->nama = $request->input('nama');
        $user->username = $request->input('username');
        $plainPassword = $request->input('password');
        $user->password = app('hash')->make($plainPassword);
        $user->no_telp = $request->input('no_telp');

        if ($request->hasFile('photo')) {
            $firstName = str_replace(' ','_', $request->input('nama'));
            $lastName = str_replace(' ','_', $request->input('username'));

            $imgName = $firstName . '_' . $lastName;
            $request->file('photo')->move(storage_path('uploads/image_user'), $imgName);

            $current_image_path = storage_path('avatar') . '/' . $user->photo;
            if (file_exists($current_image_path)) {
                unlink($current_image_path);
            }

            $user->photo = $imgName;
        }
        $user->role = $request->input('role');
        $user->alamat = $request->input('alamat');
        $user->save();

        return response()->json($user, 200);
    }
    
    public function destroy($id)
    {
        $user = User::find($id);

        if(!$user){
            abort(404);
        }
        if(Gate::denies('admin')){
            return response()->json([
                'success' => false,
                'status'=>403,
                'message' => 'You are unauthorized'

            ],403);
        }

        $user->delete();
        $message = ['message' => 'deleted successfully', 'category_id' => $id];

        return response()->json($message, 200);
    }
}
