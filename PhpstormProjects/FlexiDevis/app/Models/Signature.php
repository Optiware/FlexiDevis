<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Signature extends Model
{
    protected $table = 'signatures';
    protected $primaryKey = 'id_signature';

    const CREATED_AT = 'date_signature';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_document',
        'signature_data',
        'ip_client',
        'nom_signataire',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'id_document', 'id_document');
    }
}
