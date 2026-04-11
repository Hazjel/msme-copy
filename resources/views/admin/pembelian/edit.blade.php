@extends('adminlte::page')

@section('title', 'Edit Pembelian')

@section('content_header')
    <h1>Edit Pembelian</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('pembelian.update', $pembelian) }}" method="post">
                @csrf
                @method('PUT')
                @include('admin.pembelian._form')
            </form>
        </div>
    </div>
@stop
