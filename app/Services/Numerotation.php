<?php

namespace App\Services;

use App\Models\Document;

/**
 * Numéros des documents : D-2026-0001 pour les devis, F-2026-0001 pour les factures.
 * Chaque type a sa propre suite, qui repart à 1 chaque année.
 */
class Numerotation
{
    public static function prefixe(string $type): string
    {
        return $type === 'Facture' ? 'F' : 'D';
    }

    public static function suivant(int $idUtilisateur, string $type, ?int $annee = null): string
    {
        $annee ??= (int) date('Y');
        $debut = self::prefixe($type).'-'.$annee.'-';

        $plusGrand = Document::where('id_utilisateur', $idUtilisateur)
            ->where('numero', 'like', $debut.'%')
            ->pluck('numero')
            ->map(fn (string $numero) => (int) substr($numero, strlen($debut)))
            ->max() ?? 0;

        return sprintf('%s%04d', $debut, $plusGrand + 1);
    }
}
