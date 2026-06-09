<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    protected $table = 'documents';
    protected $primaryKey = 'id_document';

    protected $fillable = [
        'id_client',
        'id_utilisateur',
        'type_document',
        'numero',
        'date_emission',
        'date_echeance',
        'statut',
        'total_ht',
        'total_tva',
        'total_ttc',
        'remise_globale',
        'conditions_reglement',
        'notes',
    ];

    protected $casts = [
        'date_emission' => 'date',
        'date_echeance' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'id_client', 'id_client');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_utilisateur', 'id_utilisateur');
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(LigneDocument::class, 'id_document', 'id_document');
    }

    public function charges(): HasMany
    {
        return $this->hasMany(ChargeSupplementaire::class, 'id_document', 'id_document');
    }

    public function signatures(): HasMany
    {
        return $this->hasMany(Signature::class, 'id_document', 'id_document');
    }
}
