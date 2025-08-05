<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Almacen;
use App\Models\Vehiculo;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    // public function index()
    // {
    //     return view('home');
    // }
    public function index()
    {
        $cantidadVehiculos = Vehiculo::count();

        $cantidadAlmacenes = Almacen::count(); // Contar almacenes
        // return view('home', compact('cantidadAlmacenes')); // Enviar la variable a la vista
                return view('home', compact('cantidadVehiculos', 'cantidadAlmacenes'));

    }
}
