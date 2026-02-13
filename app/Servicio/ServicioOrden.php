<?php

namespace App\Servicio; 
// use App\Models\Orden;


class ServicioOrden {

    /**
     * $productos Lista de los productos de la orden
     * $idcliente es quien esta comprando 
     * $idusuario es quien esta realizando la compra 
     * $canal representa  el canal de venta
     * $idcanal representa  el indentificador del canal 
     * $fecha (opcional) representa la fecha de venta  
     * $status (opcional)  
     */

        function registrar($objeto){

    $o=new Orden();
    $o->idusuario=$objeto->idusuario;   
    $o->idcliente=$objeto->idcliente;   
    $o->total=0;

    if(isset($objeto->canal))
    $o->canal=$objeto->canal;
    else
    $o->canal='WEB';


    if(isset($objeto->idcanal))   
        $o->idcanal=$objeto->idcanal;
    else
        $o->idcanal=0;   


    if(isset($objeto->fecha))
        $o->fecha=$objeto->fecha;
       else
       $o->fecha=now();
    
    if(isset($objeto->status))
    $o->status=$objeto->status;
    else
    $o->status=1;
     
    $o->num_productos=0;
    $o->save();



        }


}

