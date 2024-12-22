@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@endsection

@section('content')
    @if ($errors->any())
        {{ dd('kurac') }}
    @endif
    <p>Welcome to this beautiful admin panel.</p>

@endsection
