<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_number',
        'date',
        'valid_until',
        'currency',
        'user_id',
        'client_id',
        'to_name',
        'to_address',
        'to_type',
        'type',
        'description',
        'bond_tax',
        'chain_id',
    ];

    protected $dates = [
        'date',
        'valid_until',
        'created_at',
        'updated_at',
    ];

    // Relação com serviços da quote
    public function services()
    {
        return $this->hasMany(QuoteService::class);
    }

    // Relação com usuário (dono da quote)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relação com cliente (destinatário)
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function transformToInvoice()
    {
        return [
            'invoice_number' => $this->quote_number,  // ou gere um novo número se preferir
            'date' => $this->date ?? now()->format('Y-m-d'),
            'payment_due' => $this->payment_due ?? now()->addDays(30)->format('Y-m-d'), // se vazio, define daqui
            'currency' => $this->currency,
            'user_id' => $this->user_id,
            'client_id' => $this->client_id,
            'to_name' => $this->to_name,
            'to_address' => $this->to_address ?? '-',
            'to_type' => $this->to_type,
            'type' => $this->type,
            'description' => $this->description,
            'bond_tax' => $this->bond_tax,

        ];
    }
}
