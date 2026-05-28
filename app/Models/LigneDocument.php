<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LigneDocument extends Model
{
    protected $table = 'ligne_documents';
    protected $primaryKey = 'id_ligne';
    public $timestamps = false;

    protected $fillable = [
        'id_document',
        'ordre',
        'description',
        'prix_unitaire',
        'quantite',
        'surface_m2',
        'temps_heures',
        'unite_mesure',
        'taux_tva',
        'remise_percent',
        'majoration_percent',
        'montant_ht',
        'montant_tva',
        'montant_ttc',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'id_document', 'id_document');
    }
}
