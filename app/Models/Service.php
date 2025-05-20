<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

}
