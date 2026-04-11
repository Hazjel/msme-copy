@extends('adminlte::page')

@section('title', 'Tambah Pembelian')

@section('content_header')
    <h1>Tambah Pembelian</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('pembelian.store') }}" method="post">
                @csrf
                @include('admin.pembelian._form', ['pembelian' => new \App\Models\Pembelian()])
            </form>
        </div>
    </div>
@stop
