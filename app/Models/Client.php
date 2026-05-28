<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $table = 'clients';
    protected $primaryKey = 'id_client';

    protected $fillable = [
        "id_utilisateur",
        'nom',
        'prenom',
        'raison_sociale',
        'adresse',
        'code_postal',
        'ville',
        'siret',
        'email',
        'telephone'
    ];

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'id_client', 'id_client');
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'id_utilisateur', 'id_utilisateur');
    }
}
