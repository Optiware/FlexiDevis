<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChargeSupplementaire extends Model
{
    protected $table = 'charge_supplementaires';
    protected $primaryKey = 'id_charge';
    public $timestamps = false;

    protected $fillable = [
        'id_document',
        'libelle',
        'montant_ht',
        'taux_tva',
        'montant_tva',
        'montant_ttc',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'id_document', 'id_document');
    }
}
