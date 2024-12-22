@extends('adminlte::page')

@section('title', 'Imported Orders')

@section('content_header')
    <h1>Imported Orders</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Orders List</h3>
            <div class="card-tools">
                <form action="{{ route('imported-data.orders') }}" method="GET" class="d-inline">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" name="search" class="form-control float-right" 
                               placeholder="Search" value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
                <div class="ml-2 d-inline">
                    <a href="{{ route('imported-data.export') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-download"></i> Export
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Order Date</th>
                        <th>Channel</th>
                        <th>SKU</th>
                        <th>Item Description</th>
                        <th>Origin</th>
                        <th>SO#</th>
                        <th>Total Price</th>
                        <th>Cost</th>
                        <th>Shipping Cost</th>
                        <th>Profit</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>{{ $order->order_date->format('Y-m-d') }}</td>
                            <td>{{ $order->channel }}</td>
                            <td>{{ $order->sku }}</td>
                            <td>{{ $order->item_description }}</td>
                            <td>{{ $order->origin }}</td>
                            <td>{{ $order->so_number }}</td>
                            <td>${{ number_format($order->total_price, 2) }}</td>
                            <td>${{ number_format($order->cost, 2) }}</td>
                            <td>${{ number_format($order->shipping_cost, 2) }}</td>
                            <td>${{ number_format($order->profit, 2) }}</td>
                            <td>
                                <a href="#" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center">No orders found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $orders->appends(request()->query())->links() }}
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop 