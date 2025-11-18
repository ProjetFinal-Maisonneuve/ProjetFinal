<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cellier extends Model
{
    use HasFactory;

    // Nom de la table dans la base de données
    protected $table = 'cellier';

    // Nom de la clé primaire
    protected $primaryKey = 'id_cellier';
    
    // Désactive les colonnes 'created_at' et 'updated_at' (selon le schéma bd_vino.pdf)
    public $timestamps = false; 

    /**
     * Les colonnes qui peuvent être assignées en masse.
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'id_utilisateur',
        'date_creation',
    ];

    /**
     * Définit la relation : Ce Cellier appartient à un Utilisateur.
     */
    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_utilisateur');
    }

    /**
     * Définit la relation : Ce Cellier contient plusieurs entrées BouteilleCellier.
     */
    public function bouteilles(): HasMany
    {
        return $this->hasMany(BouteilleCellier::class, 'id_cellier');
    }
}
