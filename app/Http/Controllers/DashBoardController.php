<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Usuario;
use App\Models\Edad;
use App\Models\Ocupacion;
use App\Models\Producto;
use App\Models\Extra;
use App\Servicio\ServicioOrden;
use App\Servicio\ServicioKPI;
use Faker\Factory as Faker;


class DashBoardController extends Controller
{
    function index()
    {
        $datos = array();
        $datos['productos']=Producto::all();
        $datos['generos']=array('Hombre', 'Mujer', 'No indica');
        $datos['edades']=Edad::all();
        $datos['canales']=array('WEB', 'APP', 'KIOSKO', 'TAQUILLA');
        $datos['ocupaciones']=Ocupacion::all();
        return view('dashboard.index')->with($datos);
    }

    function total_ventas(Request $r)
    {
        $context = $r->all();
        // dd($context);
        $servicio = new ServicioKPI();
        $objeto = new \stdClass();
        if (isset($context['idproducto']))
            $objeto->idproducto = $context['idproducto'];
        $info = $servicio->total_ventas($objeto);
        // dd($info[0]->total);
        $objeto1 = new \stdClass();
        $objeto1->tendencias = true;
        if (isset($context['idproducto']))
            $objeto1->idproducto = $context['idproducto'];
        // $objeto1->meses=6;
        $info2 = $servicio->total_ventas($objeto1);
        // dd($info2);

        $resultado = new \stdClass();
        $resultado->tendencias = $info2;
        $resultado->total = $info[0]->total;

        return response()->json($resultado);
    }


    function total_ventas_canal()
    {
        $servicio = new ServicioKPI();
        $objeto = new \stdClass();
        $info = $servicio->tendencias_canal($objeto);
        // dd($info[0]->total);

        // dd($info);

        $objeto1 = new \stdClass();
        $objeto1->tendencias = true;
        // $objeto1->meses=6;
        $info2 = $servicio->tendencias_canal($objeto1);
        // dd($info2);
        $resultado = new \stdClass();
        $resultado->tendencias = $info2;
        $resultado->canales = $info;

        return response()->json($resultado);
    }

    function total_ventas_producto()
    {
        $servicio = new ServicioKPI();
        $objeto = new \stdClass();
        $info = $servicio->ventas_productos($objeto);
        // dd($info[count($info)-1]);
        $resultado = new \stdClass();
        $resultado->top = $info[0];
        $resultado->bottom = $info[count($info) - 1];
        $resultado->productos = $info;

        $objeto1 = new \stdClass();
        $objeto1->tendencias = true;
        //  $objeto1->idproducto=3;
        $info2 = $servicio->ventas_productos($objeto1);
        dd($info2);
        $resultado->tendencias = $info2;

        return response()->json($resultado);
    }


    function total_ventas_categoria(Request $r)
    {
        $context = $r->all();

        $servicio = new ServicioKPI();
        $objeto = new \StdClass();
        if (isset($context['genero']))
            $objeto->genero = $context['genero'];
        $resultado = new \stdClass();
        $info = $servicio->total_categorias($objeto);
        $resultado->categorias = $info;
        return response()->json($resultado);
    }

    function demografico_genero(Request $r){
    
        $context = $r->all();
        $servicio = new ServicioKPI();
        $objeto = new \StdClass();
        if (isset($context['idedad']))
            $objeto->idedad = $context['idedad'];
        if (isset($context['idocupacion']))
            $objeto->idocupacion = $context['idocupacion'];
        $resultado = new \stdClass();
        $resultado = $servicio->demografico_genero($objeto);
        return response()->json($resultado);
    }


    function demografico_edad(Request $r){
         $context=$r->all();
        $servicio=new ServicioKPI();
        $objeto=new \StdClass();
        if(isset($context['idocupacion']))
            $objeto->idocupacion=$context['idocupacion'];
        if(isset($context['idgenero']))
            $objeto->idgenero=$context['idgenero'];
        $resultado=new \StdClass();
        $resultado=$servicio->demografico_edad($objeto);
        return response ()->json($resultado);
    }
}
