<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\User;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
            'qrcode' => base64_encode(QrCode::format('svg')->generate(json_encode($qrcode))),
            'saldo' => $pemasukan - $penarikan,
            'pemasukan' => $pemasukan,
            'penarikan' => $penarikan,

            'list_pemasukan' => $user->sellerTransactions()
                ->withPengirim()
                ->orderByDesc('created_at')
                ->get(),
            'list_penarikan' => $user->sellerWithdraws()
                ->withPengirim()
                ->orderByDesc('created_at')
                ->get()
        ]);
    }
}
