<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cellier extends Model
{
    use HasFactory;

    protected $table = 'celliers';

    protected $fillable = [
        'user_id',
        'nom',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bouteilles()
    {
        return $this->hasMany(Bouteille::class, 'cellier_id');
    }
}
