<?php

namespace App\Http\Controllers;

use App\Models\Cellier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Contrôleur pour la gestion des celliers.
 * 
 * Ce contrôleur gère toutes les opérations CRUD (Create, Read, Update, Delete)
 * liées aux celliers des utilisateurs authentifiés.
 * 
 * @package App\Http\Controllers
 */
class CellierController extends Controller
{
    /**
     * Affiche la liste de tous les celliers de l'utilisateur connecté.
     * 
     * Les celliers sont triés par date de création décroissante (les plus récents en premier).
     * 
     * @return View La vue contenant la liste des celliers
     */
    public function index(): View
    {
        $user = Auth::user();

        $celliers = $user->celliers()
            ->orderByDesc('date_creation')
            ->get();

        return view('cellar.index', compact('celliers'));
    }

    /**
     * Affiche le formulaire de création d'un nouveau cellier.
     * 
     * @return View La vue du formulaire de création
     */
    public function create(): View
    {
        return view('cellar.create');
    }

    /**
     * Enregistre un nouveau cellier dans la base de données.
     * 
     * Valide les données du formulaire et crée un nouveau cellier
     * associé à l'utilisateur connecté.
     * 
     * @param Request $request La requête HTTP contenant les données du formulaire
     * @return RedirectResponse Redirection vers la liste des celliers avec un message de succès
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string', // si tu ajoutes ce champ plus tard
        ]);

        $request->user()->celliers()->create([
            'nom'           => $validated['nom'],
            // 'description' => $validated['description'] ?? null, // si tu as cette colonne
        ]);

        return redirect()
            ->route('cellar.index')
            ->with('success', 'Le cellier a été créé avec succès.');
    }

    /**
     * Affiche les détails d'un cellier spécifique.
     * 
     * Vérifie que le cellier appartient bien à l'utilisateur connecté
     * avant d'afficher les détails.
     * 
     * @param Cellier $cellier Le cellier à afficher
     * @return View La vue contenant les détails du cellier
     */
    public function show(Cellier $cellier): View
    {
        $this->authorizeCellier($cellier);

        return view('celliers.show', compact('cellier'));
    }

    /**
     * Affiche le formulaire d'édition d'un cellier existant.
     * 
     * Vérifie que le cellier appartient bien à l'utilisateur connecté
     * avant d'afficher le formulaire.
     * 
     * @param Cellier $cellier Le cellier à modifier
     * @return View La vue du formulaire d'édition
     */
    public function edit(Cellier $cellier): View
    {
        $this->authorizeCellier($cellier);

        return view('cellar.update', compact('cellier'));
    }

    /**
     * Met à jour un cellier existant dans la base de données.
     * 
     * Valide les données du formulaire et met à jour les informations
     * du cellier. Vérifie que le cellier appartient bien à l'utilisateur connecté.
     * 
     * @param Request $request La requête HTTP contenant les données du formulaire
     * @param Cellier $cellier Le cellier à modifier
     * @return RedirectResponse Redirection vers la liste des celliers avec un message de succès
     */
    public function update(Request $request, Cellier $cellier): RedirectResponse
    {
        $this->authorizeCellier($cellier);

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            // 'description' => 'nullable|string',
        ]);

        $cellier->update([
            'nom' => $validated['nom'],
            // 'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('cellar.index')
            ->with('success', 'Le cellier a été mis à jour.');
    }

    /**
     * Supprime un cellier de la base de données.
     * 
     * Vérifie que le cellier appartient bien à l'utilisateur connecté
     * avant de le supprimer.
     * 
     * @param Cellier $cellier Le cellier à supprimer
     * @return RedirectResponse Redirection vers la liste des celliers avec un message de succès
     */
    public function destroy(Cellier $cellier): RedirectResponse
    {
        $this->authorizeCellier($cellier);

        $cellier->delete();

        return redirect()
            ->route('cellar.index')
            ->with('success', 'Le cellier a été supprimé.');
    }

    /**
     * Vérifie que le cellier appartient bien à l'utilisateur connecté.
     * 
     * Si le cellier n'appartient pas à l'utilisateur connecté,
     * une erreur 403 (Forbidden) est générée.
     * 
     * @param Cellier $cellier Le cellier à vérifier
     * @return void
     * @throws \Illuminate\Http\Exceptions\HttpResponseException Si l'utilisateur n'est pas autorisé
     */
    protected function authorizeCellier(Cellier $cellier): void
    {
        if ($cellier->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
