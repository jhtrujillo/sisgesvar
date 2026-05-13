<?php

namespace App\Http\Controllers;

use App\Models\Cruzamiento;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CruzamientoController extends Controller
{
    public function index()
    {
        $cruzamientos = Cruzamiento::latest()->paginate(15);

        return Inertia::render('Cruzamientos/Index', [
            'cruzamientos' => $cruzamientos
        ]);
    }
}
