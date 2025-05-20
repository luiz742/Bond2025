<?php

// app/Models/Kyc.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kyc extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'company_trade_license',
        'tax_certificate',
        'passport_copy',
        'id_proof',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
