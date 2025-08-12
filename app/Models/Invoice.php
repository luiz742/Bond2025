<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'user_id',
        'client_id',
        'invoice_number',
        'date',
        'currency',
        'payment_due',
        'to_name',
        'to_address',
        'to_tax_registration_number',
        'to_type',
        'description',
        'type', // Adicionando o campo type
        'show_conversion', // Adicionando o campo show_conversion
        'bond_tax', // Adicionando o campo bond_tax
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function services()
    {
        return $this->hasMany(InvoiceService::class);
    }

}
