@extends('layouts.app')
@section('title','Devolucion / Detalles - '.config('app.name'))
@section('header','Devolucion')
@section('breadcrumb')
    <ol class="breadcrumb">
      <li><a href="{{route('dashboard')}}"><i class="fa fa-home" aria-hidden="true"></i> Inicio</a></li>
      <li class="active"> Devolucion / Detalles </li>
    </ol>
@endsection
@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="box box-primary box-solid">
                <div class="box-body">
                    <h3 class="bg-navy padding_05em">
                        <i class="fa fa-arrow-right"></i> Devolucion [{{ $devolucion->id }}] 
                    </h3>

                    <div class="list-group col-lg-4">
                        <b>Cod. Venta</b>
                        <p class="list-group-item">{{ $devolucion->venta_id }}</p>
                    </div>

                    <div class="list-group col-lg-6">
                        <b>Fecha</b>
                        <p class="list-group-item">{{ $devolucion->fecha }}</p>
                    </div>

                    <div class="list-group col-lg-2">
                        <b>Motivo</b>
                        <p class="list-group-item">{{ $devolucion->motivo }}</p>
                    </div>
                    
                    <h3 class="bg-navy padding_05em col-lg-12">
                        <i class="fa fa-arrow-right"></i> Modelos vendidos 
                    </h3>
                    

                </div>
            </div>
            <a class="btn btn-flat btn-default" href="{{ url()->previous() }}"><i class="fa fa-reply"></i> Atras</a>
        </div>
    </div>
@endsection