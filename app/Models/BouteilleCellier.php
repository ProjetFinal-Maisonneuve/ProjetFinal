<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BouteilleCellier extends Model
{
    use HasFactory;

    // Nom de la table de jonction
    protected $table = 'bouteille_cellier';
    
    // Désactive les colonnes 'created_at' et 'updated_at' (selon le schéma bd_vino.pdf)
    public $timestamps = false; 

    /**
     * Les colonnes qui peuvent être assignées en masse.
     * @var array<int, string>
     */
    protected $fillable = [
        'id_cellier',
        'id_bouteille_catalogue',
        'quantite',
        'note_degustation',
        'date_ajout',
        'date_ouverture',
        'achetee_non_listee', 
    ];
    
    // Casting des types de données
    protected $casts = [
        'achetee_non_listee' => 'boolean',
        'date_ajout' => 'datetime',
        'date_ouverture' => 'date',
    ];

    /**
     * Définit la relation : Cette entrée appartient à un Cellier.
     */
    public function cellier(): BelongsTo
    {
        return $this->belongsTo(Cellier::class, 'id_cellier');
    }

    /**
     * Définit la relation : Cette entrée fait référence à une Bouteille du Catalogue.
     */
    public function catalogue(): BelongsTo
    {
        return $this->belongsTo(BouteilleCatalogue::class, 'id_bouteille_catalogue');
    }
}
