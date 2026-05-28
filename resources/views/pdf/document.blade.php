<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ $document->type_document }} {{ $document->numero }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; }
        .header { width: 100%; border-bottom: 1px solid #eee; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { max-width: 150px; max-height: 60px; }
        .company-info { text-align: right; float: right; }
        .client-info { margin-bottom: 30px; padding: 15px; background-color: #f9fafb; border-radius: 5px; }
        .doc-title { font-size: 24px; font-weight: bold; color: #2563eb; margin-bottom: 5px; }
        .doc-meta { font-size: 11px; color: #666; margin-bottom: 20px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #f3f4f6; padding: 10px; text-align: left; font-weight: bold; border-bottom: 2px solid #ddd; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .totals { width: 40%; float: right; }
        .totals td { font-weight: bold; }
        .total-final { background-color: #2563eb; color: white; }

        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }

        /* Utile pour nettoyer les floats */
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>

<div class="header clearfix">
    <div style="float:left;">
        @if($document->logo_path)
            <img src="{{ public_path('storage/' . $document->logo_path) }}" class="logo">
        @else
            <h1 style="margin:0;">{{ Auth::user()->raison_sociale ?? Auth::user()->name }}</h1>
        @endif
    </div>
    <div class="company-info">
        <strong>{{ Auth::user()->raison_sociale ?? Auth::user()->name }}</strong><br>
        {{ Auth::user()->adresse }}<br>
        {{ Auth::user()->code_postal }} {{ Auth::user()->ville }}<br>
        {{ Auth::user()->email }}<br>
        {{ Auth::user()->telephone }}
    </div>
</div>

<div class="clearfix">
    <div style="float:left; width: 50%;">
        <div class="doc-title">{{ strtoupper($document->type_document) }}</div>
        <div class="doc-meta">
            Numéro : <strong>{{ $document->numero }}</strong><br>
            Date : {{ \Carbon\Carbon::parse($document->date_emission)->format('d/m/Y') }}<br>
            Échéance : {{ \Carbon\Carbon::parse($document->date_echeance)->format('d/m/Y') }}
        </div>
    </div>

    <div style="float:right; width: 40%;" class="client-info">
        <span style="color:#999; font-size:10px; text-transform:uppercase;">Facturé à :</span><br>
        <strong>{{ $document->client->raison_sociale ?? $document->client->nom . ' ' . $document->client->prenom }}</strong><br>
        {{ $document->client->adresse }}<br>
        {{ $document->client->code_postal }} {{ $document->client->ville }}<br>
        {{ $document->client->email }}
    </div>
</div>

<table style="margin-top: 30px;">
    <thead>
    <tr>
        <th width="50%">Description</th>
        <th class="text-right">Qté</th>
        <th class="text-right">Prix Unit.</th>
        <th class="text-right">TVA</th>
        <th class="text-right">Total HT</th>
    </tr>
    </thead>
    <tbody>
    @foreach($document->lignes as $ligne)
        <tr>
            <td>
                <strong>{{ $ligne->description }}</strong>
            </td>
            <td class="text-right">{{ $ligne->quantite }}</td>
            <td class="text-right">{{ number_format($ligne->prix_unitaire, 2, ',', ' ') }} €</td>
            <td class="text-right">{{ $ligne->taux_tva }}%</td>
            <td class="text-right">{{ number_format($ligne->montant_ht, 2, ',', ' ') }} €</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="clearfix">
    <table class="totals">
        <tr>
            <td class="text-right">Total HT</td>
            <td class="text-right">{{ number_format($document->total_ht, 2, ',', ' ') }} €</td>
        </tr>
        <tr>
            <td class="text-right">Total TVA</td>
            <td class="text-right">{{ number_format($document->total_tva, 2, ',', ' ') }} €</td>
        </tr>
        <tr class="total-final">
            <td class="text-right" style="padding: 10px;">NET À PAYER</td>
            <td class="text-right" style="padding: 10px;">{{ number_format($document->total_ttc, 2, ',', ' ') }} €</td>
        </tr>
    </table>
</div>

@if($document->notes)
    <div style="margin-top: 40px; border-top: 1px solid #eee; padding-top: 10px;">
        <strong>Conditions & Notes :</strong><br>
        <p style="color: #666; font-style: italic;">{{ $document->notes }}</p>
    </div>
@endif

<div class="footer">
    Document généré par FlexiDevis - SIRET: {{ Auth::user()->siret ?? 'Non renseigné' }}
</div>

</body>
</html>
