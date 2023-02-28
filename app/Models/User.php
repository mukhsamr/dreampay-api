<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $guarded = ['id'];
    public $timestamps = false;

    public function buyerTopups()
    {
        return $this->hasMany(Topup::class, 'penerima');
    }

    public function cashierTopups()
    {
        return $this->hasMany(Topup::class, 'pengirim');
    }

    public function buyerTransactions()
    {
        return $this->hasMany(Transaction::class, 'pengirim');
    }

    public function sellerTransactions()
    {
        return $this->hasMany(Transaction::class, 'penerima');
    }

    public function sellerWithdraws()
    {
        return $this->hasMany(Withdraw::class, 'penerima');
    }
}
