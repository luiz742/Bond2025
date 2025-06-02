<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyMember extends Model
{
    protected $fillable = [
        'client_id',
        'name',
        'label',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
