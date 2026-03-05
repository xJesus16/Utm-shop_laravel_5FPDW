<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Usuario;
use App\Models\Edad;
use App\Models\Ocupacion;
use App\Models\Producto;
use App\Models\Extra;
use App\Servicio\ServicioOrden;
use Faker\Factory as Faker;


class DbUpController extends Controller
{

    var $generos = array('Hombre', 'Mujer', 'No indica');
    var $canales = array('WEB', 'APP', 'KIOSKO', 'TAQUILLA');


    //clientes

    function clientes()
    {
        $faker = Faker::create();
        $edades = Edad::all();
        $ocupaciones = Ocupacion::all();
        for ($i = 1; $i <= 100; $i++) {
            // usuarios
            $usuario = new Usuario();
            $usuario->idrol = 2;
            $usuario->password = bcrypt('123456');
            $usuario->email = $faker->email;
            $usuario->save();

            // usuarios

            //clientes
            $nombre = $faker->name;
            $apellido = $faker->lastname;
            $cliente = new Cliente();
            $cliente->idusuario = $usuario->id;
            $cliente->nombre = $nombre . ' ' . $apellido;
            $cliente->idedad = $edades->random()->id;
            $cliente->idocupacion = $ocupaciones->random()->id;
            $cliente->genero = $faker->randomElement($this->generos);
            $cliente->save();
        }
    }
    // clientes


    //  Ordenes

    function orden()
    {
        $faker = Faker::create();
        $servicio = new ServicioOrden();
        $clientes = Cliente::all();
        $productos = Producto::all();
        $extras = Extra::all();


        for ($i = 1; $i <= 100; $i++) {
            $objeto = new \stdClass();
            $objeto->idusuario = 0;
            $objeto->idcliente = $clientes->random()->id;
            $objeto->canal = $faker->randomElement($this->canales);
            $objeto->idcanal = 0;
            $objeto->fecha = $faker->dateTimeBetween($starDate = '-1 year', $endDate = 'now');

            $num_productos = $faker->numberBetween(1, count($productos));
            $lista_productos = $productos->random($num_productos);
            $objeto->productos = array();
            foreach ($lista_productos as $p) {


                //crear extras con ayuda del faker y la coleccion de extras
                $cantidad_extras = $faker->numberBetween(1, 10);
                $extras_random = $extras->random($cantidad_extras);

                
                $lista_extras = [];

        foreach($extras_random as $extra){
            $lista_extras[]=[
                "id" => $extra->id,
                "precio" => $extra->precio,
                "cantidad" => $faker->numberBetween(1, 10)
            ];
        }
        //crear extras aleatoriamente 

                $objeto->productos[] = [
                    "id" => $p->id,
                    "cantidad" => 1,
                    "precio" => $p->precio,
                    "extras" => $lista_extras
                    // "extras" => array()

                ];
            }
            $servicio->registrar($objeto);
            // dd( $objeto->productos);
        }
    }

    //Ordenes

}
