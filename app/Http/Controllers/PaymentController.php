<?php

namespace App\Http\Controllers;

use App\Model\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PaymentController extends Controller {
    public function index() {
        $payment = Payment::OrderBy("id", "DESC")->paginate(2)->toArray();

        $output = [
            "message" => "payment",
            "result" => $payment
        ];

        return response()->json($payment, 200);
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
        $validationRules = [
            'method' => 'required',
        ];

        $validator = \Validator::make($input, $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $payment = Payment::create($input);

        return response()->json($payment, 200);
    }

    public function show($id){
        $payment = Payment::find($id);

        if(!$payment){
            abort(404);
        }

        return response()->json($payment, 200);
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();

        $payment = Payment::find($id);

        if (!$payment) {
            abort(404);
        }
        if(Gate::denies('admin')){
            return response()->json([
                'success' => false,
                'status'=>403,
                'message' => 'You are unauthorized'

            ],403);
        }
        $validationRules = [
            'method' => 'required',
        ];

        $validator = \Validator::make($input, $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $payment->fill($input);
        $payment->save();

        return response()->json($payment, 200);
    }
    
    public function destroy($id)
    {
        $payment = Payment::find($id);

        if(!$payment){
            abort(404);
        }
        if(Gate::denies('admin')){
            return response()->json([
                'success' => false,
                'status'=>403,
                'message' => 'You are unauthorized'

            ],403);
        }

        $payment->delete();
        $message = ['message' => 'deleted successfully', 'payment_id' => $id];

        return response()->json($message, 200);
    }
}