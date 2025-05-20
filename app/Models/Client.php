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
        // adicione mais campos que você precisar, ex:
        // 'email',
        // 'phone',
    ];

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

    // (Opcional) Se quiser, pode definir casts para campos especiais, ex:
    // protected $casts = [
    //     'created_at' => 'datetime',
    //     'updated_at' => 'datetime',
    // ];
}
