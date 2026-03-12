@extends('app.master')

@section('titulo')
Ocupaciones
@endsection

@section('contenido')
<div class="col-md-12">

    <a class="btn btn-info" href="{{ route ('ocupacion.formulario')}}">Agregar</a>

    <table class="table">
        <tr>
            <td>Id</td>
            <td>Nombre</td>
        </tr>

        @foreach($lista as $elemento)
        <tr>
            <td>{{$elemento->id}}</td>

            <td>
                <a href="{{ route ('ocupacion.formulario', $elemento->id)}}">
                    {{$elemento->nombre}}
                </a>
            </td>

        </tr>
        @endforeach

    </table>

</div>
@endsection