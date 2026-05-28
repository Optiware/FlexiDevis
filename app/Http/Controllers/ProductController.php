<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // LISTE DES PRODUITS
    public function index()
    {
        $produits = Product::where('id_utilisateur', auth()->id())
            ->orderByRaw('stock_actuel <= seuil_alerte DESC')
            ->orderBy('designation')
            ->get();

        return view('products.index', compact('produits'));
    }

    // FORMULAIRE DE CRÉATION
    public function create()
    {
        return view('products.create');
    }

    // ENREGISTREMENT (NOUVEAU)
    public function store(Request $request)
    {
        $request->validate([
            'designation' => 'required|string|max:255',
            'prix_ht' => 'required|numeric|min:0',
            'stock_actuel' => 'required|integer',
        ]);

        Product::create([
            'id_utilisateur' => auth()->id(),
            'reference' => $request->reference,
            'designation' => $request->designation,
            'description' => $request->description,
            'prix_ht' => $request->prix_ht,
            'tva' => $request->tva ?? 20.00,
            'stock_actuel' => $request->stock_actuel,
            'seuil_alerte' => $request->seuil_alerte ?? 5,
        ]);

        return redirect()->route('products.index')->with('success', 'Produit ajouté au stock.');
    }

    // VOIR DÉTAILS (SHOW) - AJOUTÉ
    public function show($id)
    {
        $product = Product::where('id_produit', $id)
            ->where('id_utilisateur', auth()->id())
            ->firstOrFail();

        return view('products.show', compact('product'));
    }

    // FORMULAIRE DE MODIFICATION (EDIT) - AJOUTÉ
    public function edit($id)
    {
        $product = Product::where('id_produit', $id)
            ->where('id_utilisateur', auth()->id())
            ->firstOrFail();

        return view('products.edit', compact('product'));
    }

    // MISE À JOUR (UPDATE) - AJOUTÉ
    public function update(Request $request, $id)
    {
        $product = Product::where('id_produit', $id)
            ->where('id_utilisateur', auth()->id())
            ->firstOrFail();

        $request->validate([
            'designation' => 'required|string|max:255',
            'prix_ht' => 'required|numeric|min:0',
            'stock_actuel' => 'required|integer',
        ]);

        $product->update([
            'reference' => $request->reference,
            'designation' => $request->designation,
            'description' => $request->description,
            'prix_ht' => $request->prix_ht,
            'tva' => $request->tva,
            'stock_actuel' => $request->stock_actuel,
            'seuil_alerte' => $request->seuil_alerte,
        ]);

        return redirect()->route('products.index')->with('success', 'Produit modifié avec succès.');
    }

    // SUPPRESSION
    public function destroy($id)
    {
        $product = Product::where('id_produit', $id)
            ->where('id_utilisateur', auth()->id())
            ->firstOrFail();

        $product->delete();
        return back()->with('success', 'Produit supprimé.');
    }

    // IMPORT CSV
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');

        if (($handle = fopen($file->getPathname(), 'r')) !== false) {
            fgetcsv($handle, 1000, ';'); // Skip header

            while (($data = fgetcsv($handle, 1000, ';')) !== false) {
                if (count($data) < 4) continue;

                Product::updateOrCreate(
                    ['id_utilisateur' => auth()->id(), 'reference' => $data[0]],
                    [
                        'designation'    => $data[1],
                        'description'    => 'Import CSV',
                        'prix_ht'        => floatval(str_replace(',', '.', $data[2])),
                        'tva'            => 20.00,
                        'stock_actuel'   => intval($data[3]),
                        'seuil_alerte'   => isset($data[4]) ? intval($data[4]) : 5,
                    ]
                );
            }
            fclose($handle);
        }
        return back()->with('success', 'Importation CSV réussie !');
    }
}
