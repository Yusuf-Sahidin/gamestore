<?php

namespace App\Http\Controllers;

use App\Model\Checkout;
use App\Model\Troli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller {
    public function index() {
        $checkout = Checkout::where('id_user', Auth::user()->id)->with('troli.products')->paginate();


        return response()->json($checkout, 200);
    }
    public function store(Request $request){
        $this->validate($request, [
            'id_pembayaran' => 'required',
            'id_ekspedisi' => 'required',
            'durasi' => 'required|in:Instan,Next Day,Regular(2 - 3 hari)'
        ]);

        $troli = Troli::where('checkedout',false)->where('id_user', Auth::user()->id)->with('products')->first();
        
        if (!$troli) {
            abort(403, 'tambahkan produk ke troli terlebih dahulu!');
        }
        if (count($troli->products)==0) {
            abort(403, 'tambahkan produk ke troli terlebih dahulu');
        }
        $checkout = new Checkout();
        $checkout->id_troli=$troli->id;
        $checkout->id_user=Auth::user()->id;
        $checkout->id_pembayaran=$request->input('id_pembayaran');
        $checkout->id_ekspedisi=$request->input('id_ekspedisi');
        $checkout->durasi=$request->input('durasi');
        
        $checkout->save();
        
        $troli->checkedout=true;
        $troli->save();
        
        return response()->json($checkout, 200);
    }

    public function show($id){
        $chekout = Checkout::find($id);

        if(!$chekout){
            abort(404);
        }

        return response()->json($chekout, 200);
    }
}