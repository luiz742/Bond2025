<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteService extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'name',
        'description',
        'quantity',
        'unit_price',
        'total',
        'currency',
        'total_usd',
    ];

    // Relação com quote pai
    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    // (Opcional) Método estático para obter taxa de câmbio USD, caso use essa lógica
    public static function getExchangeRate(string $currency): float
    {
        // Exemplo fixo, adapte conforme sua regra
        $rates = [
            'USD' => 1,
            'BRL' => 0.20,
            // outras moedas...
        ];

        return $rates[$currency] ?? 1;
    }
}
