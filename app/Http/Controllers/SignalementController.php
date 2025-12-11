<?php

namespace App\Http\Controllers;

use App\Models\Signalement;
use App\Models\BouteilleCatalogue;
use Illuminate\Http\Request;

class SignalementController extends Controller
{
    /**
     * Affiche le formulaire de création d'un signalement
     */
    public function create(BouteilleCatalogue $bouteille)
    {
        return view('signalements.create', [
            'bouteille' => $bouteille
        ]);
    }

    /**
     * Valide et enregistre un signalement
     */
    public function store(Request $request, BouteilleCatalogue $bouteille)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
        ]);

        Signalement::create([
            'bouteille_catalogue_id' => $bouteille->id,
            'nom' => $validated['nom'],
            'description' => $validated['description'],
        ]);

        return redirect()
            ->back()
            ->with('success', 'Votre signalement a été envoyé avec succès.');
    }

    /**
     * Supprime un signalement
     */
    public function destroy(Signalement $signalement)
    {
        $signalement->delete();

        return redirect()
            ->back()
            ->with('success', 'Le signalement a été supprimé.');
    }
}
