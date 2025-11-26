<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bouteille extends Model
{
    use HasFactory;

    /**
     * Attributs remplissables en création/mise à jour.
     */
    protected $fillable = [
        'cellier_id', 
        'nom',        
        'pays',       
        'format',     
        'quantite',  
        'prix',
        'note_degustation',
        'rating',
        'code_saq',
    ];

    /**
     * Ici on force le prix à être un décimal avec 2 chiffres
     */
    protected function casts(): array
    {
        return [
            'prix' => 'decimal:2',
            'rating' => 'integer',
        ];
    }

    /**
     * Relation : cette bouteille appartient à un cellier.
     */
    public function cellier()
    {
        return $this->belongsTo(Cellier::class);
    }

    /**
     * Récupère l'image de la bouteille depuis le catalogue si elle existe.
     * 
     * @return string|null URL de l'image ou null si non trouvée
     */
    public function getImageFromCatalogue()
    {
        // Cherche une bouteille du catalogue avec le même nom
        $catalogueBouteille = \App\Models\BouteilleCatalogue::where('nom', $this->nom)->first();
        
        if ($catalogueBouteille && $catalogueBouteille->image) {
            return $catalogueBouteille->image;
        }
        
        return null;
    }

    public function addToCellier(Cellier $cellier, array $attributes)
    {
        
    }   
}
