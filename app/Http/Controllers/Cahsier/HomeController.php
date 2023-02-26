<?php

namespace App\Http\Controllers\Cahsier;

use App\Http\Controllers\Controller;
use App\Models\Topup;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home(User $user)
    {
        return response()->json([
            'list_buyer' => User::select('id', 'nama', 'no_hp')
                ->where('tipe', 'B')
                ->get(),
            'list_topup' => $user->cashierTopups()
                ->withPenerima()
                ->get()
        ]);
    }

    public function store(Request $request)
    {
        $nota = 'TP' . $request->cashier_id . substr(time(), 4, 6);

        $data = [
            'nota' => $nota,
            'pengirim' => $request->cashier_id,
            'penerima' => $request->buyer_id,
            'nominal' => $request->nominal
        ];

        try {
            Topup::create($data);

            $buyer = User::find($request->buyer_id);
            return response()->json([
                'nota' => $nota,
                'buyer' => $buyer->nama,
                'nominal' => $request->nominal
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Topup Gagal',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
