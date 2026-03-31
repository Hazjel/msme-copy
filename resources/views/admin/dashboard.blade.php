@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>150</h3>
                    <p>New Orders</p>
                </div>
                <div class="icon">
                    <i class="bi bi-cart"></i>
                </div>
                <a href="#" class="small-box-footer">
                    More info <i class="bi bi-arrow-right-circle"></i>
                </a>
            </div>
        </div>
    </div>
@stop

@section('css')
    {{-- Extra CSS here --}}
@stop

@section('js')
    <script>
        console.log("AdminLTE dashboard loaded.");
    </script>
@stop