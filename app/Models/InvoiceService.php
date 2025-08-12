<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
class InvoiceService extends Model
{
    protected $fillable = [
        'invoice_id',
        'name',
        'description',
        'quantity',
        'unit_price',
        'total',
        'currency',
        'total_usd',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            // 1. Buscar taxa de câmbio
            $rate = self::getExchangeRate($model->currency);

            // 2. Calcular total convertido
            $model->total_usd = round($model->total * $rate, 2);
        });
    }

    public static function getExchangeRate($currency)
    {
        $currency = strtoupper($currency);

        if ($currency === 'USD') {
            return 1;
        }

        return Cache::remember("exchange_rate_{$currency}_to_usd", 43200, function () use ($currency) {
            $apiKey = '2d2a565de51812c95d2a03f7';
            $url = "https://v6.exchangerate-api.com/v6/{$apiKey}/latest/{$currency}";

            $response = Http::get($url);

            if ($response->successful()) {
                $rates = $response->json()['conversion_rates'] ?? [];
                return $rates['USD'] ?? 1;
            }

            return 1;
        });
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
