{{-- Totaux d'un document (page et PDF). La remise globale réduit le HT, puis la TVA : HT + TVA = TTC. --}}
@php
    $totalLignesHt = (float) $document->lignes->sum('montant_ht');
    $remise = (float) $document->remise_globale;
@endphp

@if($remise > 0)
    <div class="flex justify-between text-sm text-gray-500">
        <span>Total HT des lignes</span>
        <span class="font-medium">{{ number_format($totalLignesHt, 2, ',', ' ') }} €</span>
    </div>
    <div class="flex justify-between text-sm text-red-500 font-medium">
        <span>Remise globale ({{ rtrim(rtrim(number_format($remise, 2, ',', ''), '0'), ',') }} %)</span>
        <span>- {{ number_format($totalLignesHt - (float) $document->total_ht, 2, ',', ' ') }} €</span>
    </div>
@endif

<div class="flex justify-between text-sm text-gray-500">
    <span>Total HT</span>
    <span class="font-medium">{{ number_format($document->total_ht, 2, ',', ' ') }} €</span>
</div>
<div class="flex justify-between text-sm text-gray-500">
    <span>Total TVA</span>
    <span class="font-medium">{{ number_format($document->total_tva, 2, ',', ' ') }} €</span>
</div>
