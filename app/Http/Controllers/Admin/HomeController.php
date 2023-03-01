<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topup;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdraw;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home()
    {
        $seller = Transaction::sum('nominal');
        $withdraw = Withdraw::sum('nominal');

        return response()->json([
            'total_saldo' => number_format($seller - $withdraw),
            'total_seller' => number_format($seller),
            'total_withdraw' => number_format($withdraw),
        ]);
    }

    // User
    public function listUser()
    {
        return response()->json([
            'list_user' => User::whereNot('tipe', 'A')->orderBy('nama')->get()
        ]);
    }

    public function storeUser(Request $request)
    {
        $data = [
            'no_hp' => $request->no_hp,
            'nama' => $request->nama,
            'pin' => bcrypt($request->pin),
            'tipe' => $request->tipe
        ];

        try {
            $user = User::create($data);
            return response()->json($user);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Gagal Tambah User',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function updateUser(Request $request)
    {
        $data = [
            'no_hp' => $request->no_hp,
            'nama' => $request->nama,
            'pin' => bcrypt($request->pin),
            'tipe' => $request->tipe
        ];

        try {
            $find = User::find($request->id);
            $find->update($data);

            return response()->json($find);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Gagal Edit User',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function destroyUser(Request $request)
    {
        $user = User::find($request->user_id);
        try {
            $user->delete();
            return response()->json([
                'message' => 'Berhasil Hapus User'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Gagal Hapus User',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    // Transaksi
    public function listTransaction()
    {
        return response()->json([
            'list_transaksi' => Transaction::orderByDesc('created_at')
                ->withPengirim()
                ->withPenerima()
                ->get()
                ->each(fn ($v) => $v->nominal = number_format($v->nominal)),
        ]);
    }

    // Topup
    public function listTopup()
    {
        return response()->json([
            'list_buyer' => User::where('tipe', 'B')->orderBy('nama')->get(),
            'list_topup' => Topup::orderByDesc('created_at')->get()->each(fn ($v) => $v->nominal = number_format($v->nominal)),
        ]);
    }

    public function storeTopup(Request $request)
    {
        $nota = 'TP' . $request->admin_id . substr(time(), 4, 6);

        $data = [
            'nota' => $nota,
            'pengirim' => $request->admin_id,
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
                'error' => $th->getMessage()
            ]);
        }
    }

    // Withdraw
    public function listWithdraw()
    {
        return response()->json([
            'list_buyer' => User::where('tipe', 'B')->orderBy('nama')->get(),
            'list_withdraw' => Withdraw::orderByDesc('created_at')->get()->each(fn ($v) => $v->nominal = number_format($v->nominal)),
        ]);
    }

    public function storeWithdraw(Request $request)
    {
        $nota = 'WD' . $request->admin_id . substr(time(), 4, 6);

        $data = [
            'nota' => $nota,
            'pengirim' => $request->admin_id,
            'penerima' => $request->seller_id,
            'nominal' => $request->nominal
        ];

        try {
            Withdraw::create($data);

            $seller = User::find($request->seller_id);
            return response()->json([
                'nota' => $nota,
                'seller' => $seller->nama,
                'nominal' => $request->nominal
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Withdraw Gagal',
                'error' => $th->getMessage()
            ]);
        }
    }
}
