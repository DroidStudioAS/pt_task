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
                <div class="input-group input-group-sm" style="width: 250px;">
                    <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="ml-2 d-inline">
                    <button class="btn btn-sm btn-success" id="exportBtn">
                        <i class="fas fa-download"></i> Export
                    </button>
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
                    <!-- Table content will be populated dynamically -->
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            <!-- Pagination will be added here -->
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Search functionality
            $('input[name="table_search"]').on('keyup', function() {
                // Implement search functionality
            });

            // Export functionality
            $('#exportBtn').click(function() {
                // Implement export functionality
            });
        });
    </script>
@stop 