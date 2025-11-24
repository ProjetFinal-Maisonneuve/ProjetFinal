<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Affiche la page de profil
     */
    public function index()
    {
        $user = Auth::user();
        return view('profil.index', compact('user'));
    }
}
