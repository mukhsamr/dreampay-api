<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Topup extends Model
{
    use HasFactory;

    const UPDATED_AT = NULL;
    protected $guarded = ['id'];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('d/m/Y H:i');
    }


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
