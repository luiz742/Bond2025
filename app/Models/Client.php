<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    // Quais campos podem ser preenchidos via mass assignment (ex: create, update)
    protected $fillable = [
        'user_id',
        'service_id',
        'name',
        'code_reference',
    ];

    protected static function generateUniqueCodeReference(): string
    {
        do {
            $code = str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
        } while (self::where('code_reference', $code)->exists());

        return $code;
    }

    // Definindo relacionamento com User (dono do client)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relacionamento com Service (serviço do client)
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

}
