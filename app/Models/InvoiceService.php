<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceService extends Model
{
    // app/Models/InvoiceService.php

    protected $fillable = [
        'invoice_id',
        'name',
        'description',
        'quantity',
        'unit_price',
        'total',
    ];


    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
