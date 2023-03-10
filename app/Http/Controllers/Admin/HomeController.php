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
        $topup = Topup::sum('nominal');
        $seller = Transaction::sum('nominal');
        $withdraw = Withdraw::sum('nominal');

        return response()->json([
            'total_saldo' => number_format($topup - $withdraw),
            'total_buyer' => number_format($topup - $seller),
            'total_seller' => number_format($seller - $withdraw),
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
            'list_topup' => Topup::orderByDesc('created_at')
                ->withPengirim()
                ->withPenerima()
                ->get()
                ->each(fn ($v) => $v->nominal = number_format($v->nominal)),
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
            'list_seller' => User::where('tipe', 'S')->orderBy('nama')->get(),
            'list_withdraw' => Withdraw::orderByDesc('created_at')
                ->withPengirim()
                ->withPenerima()
                ->get()
                ->each(fn ($v) => $v->nominal = number_format($v->nominal)),
        ]);
    }

    public function storeWithdraw(Request $request)
    {
        if (!$this->getSaldoSeller($request->seller_id, $request->nominal)) {
            return response()->json([
                'message' => 'Saldo tidak cukup',
            ], 403);
        }

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

    public function getSaldoSeller($seller_id, $nominal)
    {
        $user = User::withSum('sellerTransactions as masuk', 'nominal')
            ->withSum('sellerWithdraws as withdraw', 'nominal')
            ->find($seller_id);

        return $user->masuk - $nominal - $user->withdraw >= 0;
    }

    public function bestSeller()
    {
        $seller = User::where('tipe', 'S')
            ->select('id', 'nama', 'no_hp')
            ->withSum('sellerTransactions as pemasukan', 'nominal')
            ->orderByDesc('pemasukan')
            ->get();

        return response()->json([
            'rekap' => $seller
        ]);
    }

    public function bestBuyer()
    {
        $buyer = User::where('tipe', 'B')
            ->select('id', 'nama', 'no_hp')
            ->withSum('buyerTopups as topup', 'nominal')
            ->withSum('buyerTransactions as pengeluaran', 'nominal')
            ->orderByDesc('pengeluaran')
            ->get()
            ->each(fn ($v) => $v->saldo = $v->topup - $v->pengeluaran);

        return response()->json([
            'rekap' => $buyer
        ]);
    }

    public function bestTopup()
    {
        $buyer = User::where('tipe', 'B')
            ->select('id', 'nama', 'no_hp')
            ->withSum('buyerTopups as topup', 'nominal')
            ->orderByDesc('topup')
            ->get()
            ->each(fn ($v) => $v->topup = number_format($v->topup));

        return response()->json([
            'total' => $buyer->count(),
            'rekap' => $buyer,
        ]);
    }

    public function report()
    {
        $cahierTopup = Topup::withPengirim()
            ->withPenerima()
            ->get()
            ->groupBy('pengirim');

        return response()->json([
            'cahierTopup' => $cahierTopup,
        ]);
    }
}
