<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class Client extends Model
{
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

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

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

    // Cria notificação ao criar um novo cliente
    public function notifyClientCreated()
    {
        Notification::create([
            'type' => 'client_created',
            'client_id' => $this->id,
            'user_id' => $this->user_id,
            'service_id' => $this->service_id,
        ]);
    }

    public function familyMembers()
    {
        return $this->hasMany(FamilyMember::class);
    }


    // Cria notificação de upload de documento
    public function notifyDocumentUploaded()
    {
        Notification::create([
            'type' => 'document_uploaded',
            'client_id' => $this->id,
            'user_id' => Auth::id(), // ou use $this->user_id se apropriado
            'service_id' => $this->service_id,
        ]);
    }
}
