<?php

namespace App\Http\Controllers;

use App\Models\ListeAchat;
use App\Models\BouteilleCatalogue;
use App\Models\Bouteille;
use Illuminate\Http\Request;
use App\Models\Pays;
use App\Models\TypeVin;
use App\Models\Region;


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

        $pays = Pays::all();
        $types = TypeVin::all();
        $regions = Region::all();
        $millesimes = BouteilleCatalogue::select('millesime')
            ->whereNotNull('millesime')
            ->distinct()
            ->orderBy('millesime', 'desc')
            ->get();


        return view('liste_achat.index', compact('items', 'totalPrice', 'totalItem', 'avgPrice', 'pays', 'types', 'regions', 'millesimes'));
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

        $user = auth()->user();
        $cellierId = $request->cellier_id;

        // Vérifier que le cellier appartient à l'utilisateur
        $cellier = $user->celliers()->find($cellierId);

        if (!$cellier) {
            return response()->json([
                'success' => false,
                'message' => 'Cellier non trouvé ou vous n\'avez pas accès à ce cellier.',
            ], 403);
        }

        $quantite = $item->quantite;
        // Charger la bouteille du catalogue avec ses relations nécessaires
        $bouteilleCatalogue = $item->bouteilleCatalogue;

        // Charger les relations si elles ne sont pas déjà chargées
        if (!$bouteilleCatalogue->relationLoaded('pays')) {
            $bouteilleCatalogue->load('pays');
        }
        if (!$bouteilleCatalogue->relationLoaded('typeVin')) {
            $bouteilleCatalogue->load('typeVin');
        }

        // Vérifier si la bouteille existe déjà dans ce cellier
        // Rechercher par nom et cellier_id (comme dans ajoutBouteilleApi)
        $bouteilleExistante = Bouteille::where('cellier_id', $cellierId)
            ->where('nom', $bouteilleCatalogue->nom)
            ->first();

        if ($bouteilleExistante) {
            // Augmenter la quantité si la bouteille existe déjà
            $bouteilleExistante->quantite += $quantite;
            // Mettre à jour le code_saq si ce n'est pas déjà défini
            if (empty($bouteilleExistante->code_saq) && !empty($bouteilleCatalogue->code_saQ)) {
                $bouteilleExistante->code_saq = $bouteilleCatalogue->code_saQ;
            }
            $bouteilleExistante->save();
        } else {
            // Créer une nouvelle bouteille dans le cellier
            $nouvelleBouteille = new Bouteille();
            $nouvelleBouteille->cellier_id = $cellierId;
            $nouvelleBouteille->nom = $bouteilleCatalogue->nom;
            $nouvelleBouteille->pays = $bouteilleCatalogue->pays ? $bouteilleCatalogue->pays->nom : null;
            $nouvelleBouteille->format = $bouteilleCatalogue->volume;
            $nouvelleBouteille->quantite = $quantite;
            $nouvelleBouteille->prix = $bouteilleCatalogue->prix;
            $nouvelleBouteille->code_saq = $bouteilleCatalogue->code_saQ;

            // Ajouter type et millésime si disponibles
            if ($bouteilleCatalogue->typeVin) {
                $nouvelleBouteille->type = $bouteilleCatalogue->typeVin->nom;
            }
            if ($bouteilleCatalogue->millesime) {
                $nouvelleBouteille->millesime = $bouteilleCatalogue->millesime;
            }

            $nouvelleBouteille->save();
        }

        // Supprimer de la liste d'achat
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => "L'item a été transféré dans votre cellier.",
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

    public function search(Request $request)
    {
        $user = auth()->user();

        $query = $user->listeAchat()->with('bouteilleCatalogue');

        // SEARCH TEXT
        if ($request->search) {
            $query->whereHas('bouteilleCatalogue', function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->search . '%');
            });
        }

        // PAYS
        if ($request->pays) {
            $query->whereHas('bouteilleCatalogue', function ($q) use ($request) {
                $q->where('id_pays', $request->pays);
            });
        }

        // TYPE
        if ($request->type) {
            $query->whereHas('bouteilleCatalogue', function ($q) use ($request) {
                $q->where('id_type_vin', $request->type);
            });
        }

        // REGION
        if ($request->region) {
            $query->whereHas('bouteilleCatalogue', function ($q) use ($request) {
                $q->where('id_region', $request->region);
            });
        }

        // MILLÉSIMES
        if ($request->millesime) {
            $query->whereHas('bouteilleCatalogue', function ($q) use ($request) {
                $q->where('millesime', $request->millesime);
            });
        }

        // PRIX MIN / MAX
        if ($request->prix_min || $request->prix_max) {
            $query->whereHas('bouteilleCatalogue', function ($q) use ($request) {
                if ($request->prix_min) $q->where('prix', '>=', $request->prix_min);
                if ($request->prix_max) $q->where('prix', '<=', $request->prix_max);
            });
        }

        // SORTING
        if ($request->sort_by && in_array($request->sort_direction, ['asc', 'desc'])) {
            $query->whereHas('bouteilleCatalogue', function ($q) use ($request) {
                $q->orderBy($request->sort_by, $request->sort_direction);
            });
        }

        $items = $query->paginate(10);
        $count = $items->total();

        return response()->json([
            'html' => view('liste_achat._liste_achat_list', compact('items', 'count'))->render()
        ]);
    }
}
