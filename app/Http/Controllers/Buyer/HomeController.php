<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home(User $user)
    {
        $user->load([
            'buyerTopups'       => fn ($query) => $query->withPengirim(),
            'buyerTransactions' => fn ($query) => $query->withPenerima(),
        ]);

        $total_topup = $user->buyerTopups->sum('nominal');
        $total_transaction = $user->buyerTransactions->sum('nominal');

        return response()->json([
            'saldo' => $total_topup - $total_transaction,

            'total_topup' => $total_topup,
            'total_pengeluaran' => $total_transaction,

            'list_topup' => $user->buyerTopups()->withPengirim()->orderByDesc('created_at')->get(),
            'list_pengeluaran' => $user->buyerTransactions()->withPenerima()->orderByDesc('created_at')->get()
        ]);
    }

    public function store(Request $request)
    {
        if (!$this->getSaldo($request->buyer_id)) {
            return response()->json([
                'message' => 'Saldo tidak cukup',
            ], 403);
        }

        $seller = User::firstWhere('no_hp', $request->seller_no_hp);
        $nota = 'TR' . $request->buyer_id . substr(time(), 4, 6);

        $data = [
            'nota' => $nota,
            'pengirim' => $request->buyer_id,
            'penerima' => $seller->id,
            'nominal' => str_replace('.', '', $request->nominal)
        ];

        try {
            Transaction::create($data);

            return response()->json([
                'nota' => $nota,
                'seller' => $seller->nama,
                'nominal' => $request->nominal
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Pembayaran Gagal',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    // 

    private function getSaldo($buyer_id): bool
    {
        $user = User::withSum('buyerTopups as topup', 'nominal')
            ->withSum('buyerTransactions as transaction', 'nominal')
            ->find($buyer_id);

        return $user->topup - $user->transaction >= 0;
    }
}
