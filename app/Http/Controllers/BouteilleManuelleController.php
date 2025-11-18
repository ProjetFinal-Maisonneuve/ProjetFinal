<?php

namespace App\Http\Controllers;

use App\Models\Cellier;
use App\Models\Bouteille; // adapte si le modèle a un autre nom
use Illuminate\Http\Request;

class BouteilleManuelleController extends Controller
{
    /**
     * Affiche le formulaire d'ajout manuel d'une bouteille
     * dans un cellier donné.
     */
    public function create(Cellier $cellier)
    {
        return view('bouteilles.ajout-manuelle', [
            'cellier' => $cellier,
        ]);
    }

    /**
     * Traite l'ajout manuel d'une bouteille.
     */
    public function store(Request $request, Cellier $cellier)
    {
        // Validation des champs (adapte les noms/colonnes selon ta migration)
        $validated = $request->validate([
            'nom'        => 'required|string|max:255',
            'pays'       => 'nullable|string|max:255',
            'format'     => 'nullable|string|max:50',
            'quantite'   => 'required|integer|min:1',
            'prix'       => ['required', 'numeric', 'between:0,9999.99'],
        ]);

        // S'assurer que le prix est bien en décimal (2 chiffres après la virgule)
        $prixDecimal = number_format($validated['prix'], 2, '.', '');

        // Création de la bouteille dans ce cellier
        Bouteille::create([
            'cellier_id' => $cellier->id,
            'nom'        => $validated['nom'],
            'pays'       => $validated['pays'] ?? null,
            'format'     => $validated['format'] ?? null,
            'quantite'   => $validated['quantite'],
            'prix'       => $prixDecimal,
        ]);

        return redirect()
            ->route('celliers.show', $cellier->id) // ou celliers.index selon votre design
            ->with('success', 'Bouteille ajoutée manuellement avec succès.');
    }
}
