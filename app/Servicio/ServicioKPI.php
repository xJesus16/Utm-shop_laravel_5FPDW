<?php

namespace App\Servicio;

use App\Models\Orden;
use App\Models\DetalleOrden;
use App\Models\ExtraOrden;

use Illuminate\Support\Facades\DB;

class ServicioKPI
{

    //Key
    //Performance
    //Indicator

    // Total de venteas de los ultimos N meses 
    // Atributos 
    //Meses - Cuantos meses antes genero el kpi


    /*
         Sin filtro de producto
        select sum(orden.total)
        from orden
        where DATE_SUB(now(),INTERVAL 3 MONTH)

         Sin filtro 
        select sum(orden.total)
        ,DATE_FORMAT(orden.fecha, '%m-%Y') as fecha
        from orden
        where DATE_SUB(now(),INTERVAL 3 MONTH)
         group by DATE_FORMAT(orden.fecha, '%m-%Y')
        order by DATE_FORMAT(orden.fecha, '%Y-%m') desc

         Sin filtro de producto
        select sum(detalle_orden.cantidad*detalle_orden.precio)
            ,DATE_FORMAT(orden.fecha, '%m-%Y') as fecha 
        from orden
        join detalle_orden on orden.id=detalle_orden.idorden
        where DATE_SUB(now(),INTERVAL 3 MONTH)
        and detalle_orden.idproducto=13
        group by DATE_FORMAT(orden.fecha, '%m-%Y')
        order by DATE_FORMAT(orden.fecha, '%Y-%m') desc

        */

    function total_ventas($objeto)
    {
        if (!isset($objeto->meses)) {
            $objeto->meses = 3;
        }

        if (!isset($objeto->tendencias)) {
            $objeto->tendencias = false;
        }

        if (!isset($objeto->idproducto)) {
            $objeto->idproducto = 0;
        }


        // Defino la consulta base
        if ($objeto->idproducto == 0) {
            // Sin filtro de producto
            $consulta = DB::table('orden')
                ->select(
                    DB::RAW('sum(orden.total) as total')
                )
                ->whereRAW("orden.fecha>=DATE_SUB(now(),INTERVAL " . $objeto->meses . " MONTH)");
        } else {
            //Con filtro de producto
            $consulta = DB::table('orden')
                ->join('detalle_orden', 'detalle_orden.idorden', '=', 'orden.id')
                ->select(
                    DB::RAW('sum(detalle_orden.precio * detalle_orden.cantidad) as total')
                )
                ->whereRAW("orden.fecha>=DATE_SUB(now(),INTERVAL " . $objeto->meses . " MONTH)")
                ->where('detalle_orden.idproducto', $objeto->idproducto);
        }
        // $consulta = DB::table('orden')
        //     ->select(
        //         DB::RAW('sum(orden.total) as total')
        //     )
        //     ->whereRaw("orden.fecha>=DATE_SUB(now(),INTERVAL " . $objeto->meses . " MONTH)");

        // 2.Configuro la consulta

        if ($objeto->tendencias) {
            $consulta->groupBy(DB::RAW("DATE_FORMAT(orden.fecha,'%m-%Y')"))
                ->orderBy(DB::RAW("DATE_FORMAT(orden.fecha,'%m-%Y')"), "desc")
                ->addSelect(DB::RAW("DATE_FORMAT(orden.fecha,'%m-%Y') as fecha"));
        }


        //    3.-Ejecuto la consulta
        return $consulta->get();
    }

    /*
    Consulta base 
            select orden.canal
            ,sum(orden.total)
        from orden 
        where orden.fecha>=DATE_SUB(now(),INTERVAL 3 MONTH)
        group by orden.canal
        order by sum(orden.total) desc
        
            select orden.canal
            ,DATE_FORMAT(orden.fecha, '%m-%Y')
            ,sum(orden.total)
        from orden 
        where orden.fecha>=DATE_SUB(now(),INTERVAL 3 MONTH)
        group by orden.canal, DATE_FORMAT(orden.fecha, '%m-%Y')
        order by DATE_FORMAT(orden.fecha, '%m-%Y') desc
    */


    function tendencias_canal($objeto)
    {
        if (!isset($objeto->meses)) {
            $objeto->meses = 3;
        }

        if (!isset($objeto->tendencias)) {
            $objeto->tendencias = false;
        }


        // Defino la consulta base
        $consulta = DB::table('orden')
            ->select(
                DB::RAW('sum(orden.total) as total'),
                "orden.canal"
            )
            ->whereRAW("orden.fecha>=DATE_SUB(now(),INTERVAL " . $objeto->meses . " MONTH)")
            ->groupBy("orden.canal");
        // ->orderBy(DB::RAW('sum(orden.total) as total'),"asc");

        if ($objeto->tendencias) {
            $consulta->groupBy(DB::RAW("DATE_FORMAT(orden.fecha,'%m-%Y')"))
                ->orderBy(DB::RAW("DATE_FORMAT(orden.fecha,'%m-%Y')"), "desc")
                ->addSelect(DB::RAW("DATE_FORMAT(orden.fecha,'%m-%Y') as fecha"));
        }




        //    3.-Ejecuto la consulta
        return $consulta->get();
    }

    // Producto mas vendido
    // Producto menos vendido
    // Tendencias por producto
    /*
                    SELECT 
                producto.nombre,
                SUM(detalle_orden.cantidad * detalle_orden.precio) AS total
            FROM detalle_orden
            JOIN producto 
                ON detalle_orden.idproducto = producto.id
            JOIN orden 
                ON orden.id = detalle_orden.idorden
            WHERE orden.fecha >= DATE_SUB(NOW(), INTERVAL 3 MONTH)
            GROUP BY producto.id
            ORDER BY SUM(detalle_orden.cantidad * detalle_orden.precio) DESC;


        */
    function ventas_productos($objeto)
    {
        if (!isset($objeto->meses)) {
            $objeto->meses = 3;
        }
        if (!isset($objeto->tendencias)) {
            $objeto->tendencias = false;
        }
        if (!isset($objeto->idproducto)) {
            $objeto->idproducto = 0;
        }


        $consulta = DB::table('orden')
            ->join('detalle_orden', 'detalle_orden.idorden', '=', 'orden.id')
            ->join('producto', 'detalle_orden.idproducto', '=', 'producto.id')
            ->select(
                "producto.nombre",
                DB::RAW("SUM(detalle_orden.cantidad * detalle_orden.precio)as total")
            )

            ->whereRAW("orden.fecha >= DATE_SUB(NOW(), INTERVAL " . $objeto->meses . " MONTH)")
            ->groupBy("producto.id")
            ->orderByRAW("SUM(detalle_orden.cantidad * detalle_orden.precio) desc");

        if ($objeto->tendencias) {
            $consulta->groupBy(DB::RAW("DATE_FORMAT(orden.fecha,'%m-%Y')"))
                ->orderByRAW("DATE_FORMAT(orden.fecha,'%m-%Y') asc")
                ->addSelect(DB::RAW("DATE_FORMAT(orden.fecha,'%m-%Y') as fecha"));
        }



        if ($objeto->idproducto != 0) {
            $consulta->where("producto.id", $objeto->idproducto);
        }

        return $consulta->get();
    }

    /*
        Sin filtro de cliente
         select categoria.nombre
          ,sum(detalle_orden.precio*detalle_orden.cantidad)
          from detalle_orden
          join detalle_orden on orden.id=detalle_orden.idorden
          join producto on producto.id=detalle_orden.idproducto
          join categorio on categoria categoria.id=producto.categoria
          where DATE_SUB(now(), INTERVAL 3 MONTH)
            

            Con filtro de clientes 
         select categoria.nombre
          ,sum(detalle_orden.precio*detalle_orden.cantidad)
          from detalle_orden
          join detalle_orden on orden.id=detalle_orden.idorden
          join producto on producto.id=detalle_orden.idproducto
          join categorio on categoria categoria.id=producto.categoria
          join cliente on orden.idcliente=cliente.id
          where DATE_SUB(now(), INTERVAL 3 MONTH)
            and cliente.genero='Mujer'
          
    */



    function total_categorias($objeto)
    {
        if (!isset($objeto->genero))
            $objeto->genero = '';

        if (!isset($objeto->meses)) {
            $objeto->meses = 3;
        }

        
        $consulta=DB::table('orden')
        ->join('detalle_orden', 'detalle_orden.idorden', '=', 'orden.id')
        ->join('producto', 'detalle_orden.idproducto', '=', 'producto.id')
        ->join('categoria', 'categoria.id', '=', 'producto.categoria')
        ->select (
            "categoria.nombre"
            ,DB::RAW("SUM(detalle_orden.cantidad*detalle_orden.precio) as total")
        )
        ->whereRaw("orden.fecha>=DATE_SUB(now(), INTERVAL ".$objeto->meses." MONTH)")
        ->groupBy("categoria.nombre");

        if($objeto->genero!=''){
            $consulta->join('cliente', 'orden.idcliente', '=', 'cliente.id')
                     ->where('cliente.genero', $objeto->genero);
        }

        return $consulta->get();
    }
}
