<?php

use App\Services\CalculDocument;

it('calcule chaque ligne : quantité × prix, remise de ligne, puis TVA', function () {
    $calcul = CalculDocument::calculer([
        ['description' => 'Pose carrelage', 'quantite' => 10, 'prix_unitaire' => 45, 'tva' => 10, 'remise_percent' => 10],
        ['description' => 'Déplacement', 'quantite' => 1, 'prix_unitaire' => 50, 'tva' => 20],
    ]);

    expect($calcul['lignes'][0])->toMatchArray([
        'ordre' => 1,
        'montant_ht' => 405.0,
        'montant_tva' => 40.5,
        'montant_ttc' => 445.5,
    ]);
    expect($calcul['total_ht'])->toBe(455.0)
        ->and($calcul['total_tva'])->toBe(50.5)
        ->and($calcul['total_ttc'])->toBe(505.5)
        ->and($calcul['remise_ht'])->toBe(0.0);
});

it('applique la remise globale au HT puis à la TVA : HT + TVA = TTC', function () {
    $calcul = CalculDocument::calculer([
        ['quantite' => 2, 'prix_unitaire' => 100, 'tva' => 20],
    ], 10);

    expect($calcul['total_lignes_ht'])->toBe(200.0)
        ->and($calcul['remise_ht'])->toBe(20.0)
        ->and($calcul['total_ht'])->toBe(180.0)
        ->and($calcul['total_tva'])->toBe(36.0)
        ->and($calcul['total_ttc'])->toBe(216.0)
        ->and($calcul['total_ht'] + $calcul['total_tva'])->toBe($calcul['total_ttc']);
});

it('arrondit au centime et donne un titre aux lignes sans description', function () {
    $calcul = CalculDocument::calculer([
        ['quantite' => 3, 'prix_unitaire' => 0.333, 'tva' => 5.5],
    ]);

    expect($calcul['lignes'][0]['montant_ht'])->toBe(1.0)
        ->and($calcul['lignes'][0]['montant_tva'])->toBe(0.06)
        ->and($calcul['lignes'][0]['description'])->toBe('Sans titre');
});

it('rend des totaux nuls sans ligne', function () {
    expect(CalculDocument::calculer([], 15))->toMatchArray([
        'total_ht' => 0.0, 'total_tva' => 0.0, 'total_ttc' => 0.0,
    ]);
});
