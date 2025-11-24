<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BouteilleCatalogue;

class CatalogueController extends Controller
{
    public function index()
    {
        // Récupère les 10 dernières bouteilles importées avec leurs relations
        $bouteilles = BouteilleCatalogue::with(['pays', 'typeVin'])
            ->orderBy('date_import', 'desc')
            ->paginate(10);


        return view('bouteilles.catalogue', compact('bouteilles'));
    }
}
