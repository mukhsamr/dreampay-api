<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\User;

class HomeController extends Controller
{
    public function home(User $user)
    {
        $user->load(['sellerTransactions', 'sellerWithdraws']);

        $penarikan = $user->sellerWithdraws->sum('nominal');
        $pemasukan = $user->sellerTransactions->sum('nominal');

        $qrcode = [
            'nama' => $user->nama,
            'no_hp' => $user->no_hp
        ];

        return response()->json([
            'qrcode' => $qrcode,
            'saldo' => number_format($pemasukan - $penarikan),
            'pemasukan' => number_format($pemasukan),
            'penarikan' => number_format($penarikan),

            'list_pemasukan' => $user->sellerTransactions()
                ->withPengirim()
                ->orderByDesc('created_at')
                ->get()
                ->each(fn ($v) => $v->nominal = number_format($v->nominal)),
            'list_penarikan' => $user->sellerWithdraws()
                ->withPengirim()
                ->orderByDesc('created_at')
                ->get()
                ->each(fn ($v) => $v->nominal = number_format($v->nominal)),
        ]);
    }
}
