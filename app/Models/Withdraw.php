<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    use HasFactory;

    const UPDATED_AT = NULL;
    protected $guarded = ['id'];
    protected $casts = ['created_at' => 'datetime:m/d/Y H:i'];

    // Scope
    public function scopeWithPengirim($query)
    {
        $query->addSelect([
            'pengirim' => User::select('nama')
                ->whereColumn('id', $this->getTable() . '.pengirim')
                ->take(1)
        ]);
    }

    public function scopeWithPenerima($query)
    {
        $query->addSelect([
            'penerima' => User::select('nama')
                ->whereColumn('id', $this->getTable() . '.penerima')
                ->take(1)
        ]);
    }
}
