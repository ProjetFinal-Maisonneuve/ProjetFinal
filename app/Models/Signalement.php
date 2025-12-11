<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Signalement extends Model
{
    use HasFactory;

    protected $fillable = [
        'bouteille_catalogue_id',
        'nom',
        'description',
    ];

    public function bouteilleCatalogue()
    {
        return $this->belongsTo(BouteilleCatalogue::class, 'bouteille_catalogue_id');
    }
}
