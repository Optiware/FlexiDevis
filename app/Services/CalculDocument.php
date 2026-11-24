<?php

namespace App\Services;

/**
 * Calcul des montants d'un devis ou d'une facture.
 *
 * - Chaque ligne : quantité × prix unitaire, moins sa remise (%), puis sa TVA.
 * - La remise globale (%) réduit la base HT, donc aussi la TVA : HT + TVA = TTC.
 * Les montants sont arrondis au centime.
 */
class CalculDocument
{
    /**
     * @param  array<int, array{quantite?: mixed, prix_unitaire?: mixed, tva?: mixed, remise_percent?: mixed, description?: mixed}>  $lignes
     * @return array{lignes: array<int, array<string, mixed>>, total_lignes_ht: float, remise_ht: float, total_ht: float, total_tva: float, total_ttc: float}
     */
    public static function calculer(array $lignes, float $remiseGlobale = 0): array
    {
        $resultat = [];
        $totalLignesHt = 0.0;
        $tvaLignes = 0.0;

        foreach (array_values($lignes) as $ordre => $ligne) {
            $quantite = (float) ($ligne['quantite'] ?? 0);
            $prix = (float) ($ligne['prix_unitaire'] ?? 0);
            $tauxTva = (float) ($ligne['tva'] ?? 20);
            $remise = (float) ($ligne['remise_percent'] ?? 0);

            $ht = round($quantite * $prix * (1 - $remise / 100), 2);
            $tva = round($ht * $tauxTva / 100, 2);

            $resultat[] = [
                'ordre' => $ordre + 1,
                'description' => trim((string) ($ligne['description'] ?? '')) ?: 'Sans titre',
                'quantite' => $quantite,
                'prix_unitaire' => $prix,
                'taux_tva' => $tauxTva,
                'remise_percent' => $remise,
                'montant_ht' => $ht,
                'montant_tva' => $tva,
                'montant_ttc' => round($ht + $tva, 2),
            ];
            $totalLignesHt += $ht;
            $tvaLignes += $tva;
        }

        $coefficient = 1 - $remiseGlobale / 100;
        $totalHt = round($totalLignesHt * $coefficient, 2);
        $totalTva = round($tvaLignes * $coefficient, 2);

        return [
            'lignes' => $resultat,
            'total_lignes_ht' => round($totalLignesHt, 2),
            'remise_ht' => round($totalLignesHt - $totalHt, 2),
            'total_ht' => $totalHt,
            'total_tva' => $totalTva,
            'total_ttc' => round($totalHt + $totalTva, 2),
        ];
    }
}
