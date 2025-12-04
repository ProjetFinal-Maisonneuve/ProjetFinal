<?php

namespace App\Http\Controllers;

use App\Models\ListeAchat;
use App\Models\BouteilleCatalogue;
use Illuminate\Http\Request;

class ListeAchatController extends Controller
{
    /**
     * Affiche la liste d'achat de l'utilisateur courant.
     */
    public function index()
    {
        $items = auth()->user()
            ->listeAchat()
            ->with('bouteilleCatalogue')
            ->orderBy('achete')
            ->orderBy('date_ajout', 'desc')
            ->paginate(10);

        $allItems = auth()->user()
            ->listeAchat()
            ->with('bouteilleCatalogue')
            ->get();

        $totalPrice = $allItems->sum(fn($item) => $item->bouteilleCatalogue->prix * $item->quantite);
        $totalItem = $allItems->sum(fn($item) => $item->quantite);
        $avgPrice = $allItems->count() ? $totalPrice / $allItems->count() : 0;


        return view('liste_achat.index', compact('items', 'totalPrice', 'totalItem', 'avgPrice'));
    }

    /**
     * Ajoute une bouteille à la liste d'achat
     */
    public function store(Request $request)
    {
        $request->validate([
            'bouteille_catalogue_id' => 'required|exists:bouteille_catalogue,id',
            'quantite' => 'nullable|integer|min:1'
        ]);

        $user = auth()->user();
        $bottleId = $request->bouteille_catalogue_id;
        $qty = $request->quantite ?? 1;

        // Vérifier si déjà existant
        $item = ListeAchat::where('user_id', $user->id)
            ->where('bouteille_catalogue_id', $bottleId)
            ->first();

        if ($item) {
            $item->increment('quantite', $qty);

            return response()->json([
                'success' => true,
                'message' => 'Quantité augmentée dans votre liste d’achat.'
            ]);
        }

        // Sinon créer l'entrée
        ListeAchat::create([
            'user_id' => $user->id,
            'bouteille_catalogue_id' => $bottleId,
            'quantite' => $qty,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bouteille ajoutée à votre liste d’achat.'
        ]);
    }

    public function transfer(Request $request, ListeAchat $item)
    {
        $request->validate([
            'cellier_id' => 'required|exists:celliers,id',
        ]);

        $cellierId = $request->cellier_id;
        $quantite = $item->quantite;

        $bouteille = $item->bouteilleCatalogue;

        // Vérifier si la bouteille existe déjà dans ce cellier
        $cellierItem = \DB::table('bouteille_cellier')
            ->where('id_cellier', $cellierId)
            ->where('id_bouteille_catalogue', $bouteille->id)
            ->first();

        if ($cellierItem) {

            \DB::table('bouteille_cellier')
                ->where('id_cellier', $cellierId)
                ->where('id_bouteille_catalogue', $bouteille->id)
                ->update([
                    'quantite'   => $cellierItem->quantite + $quantite,
                    'date_ajout' => now(),
                ]);
        } else {

            \DB::table('bouteille_cellier')->insert([
                'id_cellier'             => $cellierId,
                'id_bouteille_catalogue' => $bouteille->id,
                'quantite'               => $quantite,
                'date_ajout'             => now(),
                'achetee_non_listee'     => 0,
            ]);
        }

        // Supprimer de la liste d’achat
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => "L’item a été transféré dans votre cellier.",
        ]);
    }

    /**
     * Modifier quantité ou statut acheté
     */
    public function update(Request $request, ListeAchat $item)
    {
        $item->update($request->only(['quantite', 'achete']));

        return back()->with('success', 'Liste mise à jour.');
    }

    /**
     * Supprimer un item
     */
    public function destroy(ListeAchat $item)
    {
        $item->delete();

        return back()->with('success', 'Élément supprimé de votre liste d’achat.');
    }
}
