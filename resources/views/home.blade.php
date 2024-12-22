@extends('adminlte::page')

@section('title', 'Dashboard')

@dd(auth()->user()->hasPermission('user-management'))

@section('content_header')
    <h1>Dashboard</h1>
@endsection

@section('content')
    <p>Welcome to this beautiful admin panel.</p>
@endsection
