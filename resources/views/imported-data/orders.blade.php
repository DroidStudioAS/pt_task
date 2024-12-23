@extends('adminlte::page')

@section('title', 'Imported Orders')

@section('content_header')
    <h1>
        Imported Orders
        @if(isset($import))
            <small>From import: {{ $import->file_name }}</small>
        @endif
    </h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Orders List</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-toggle="collapse" data-target="#searchDrawer" 
                        title="Toggle Search Filters">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <!-- Search Drawer -->
        <div class="collapse" id="searchDrawer">
            <div class="card-body border-bottom">
                <form action="{{ route('imported-data.orders') }}" method="GET">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <!-- Global Search -->
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </span>
                                    </div>
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Global Search" value="{{ request('search') }}"
                                           data-toggle="tooltip" 
                                           title="Search across all fields">
                                </div>
                            </div>

                            <!-- Date Range -->
                            <div class="form-group">
                                <label>Date Range</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-calendar"></i>
                                        </span>
                                    </div>
                                    <input type="date" name="date_from" class="form-control" 
                                           value="{{ request('date_from') }}"
                                           data-toggle="tooltip" 
                                           title="Filter by start date">
                                    <div class="input-group-append input-group-prepend">
                                        <span class="input-group-text">to</span>
                                    </div>
                                    <input type="date" name="date_to" class="form-control" 
                                           value="{{ request('date_to') }}"
                                           data-toggle="tooltip" 
                                           title="Filter by end date">
                                </div>
                            </div>

                            <!-- Sort Options -->
                            <div class="form-group">
                                <label>Sort Options</label>
                                <div class="input-group">
                                    <select name="sort_by" class="form-control"
                                            data-toggle="tooltip" 
                                            title="Choose field to sort by">
                                        @foreach($fillableFields as $field)
                                            <option value="{{ $field }}" 
                                                {{ request('sort_by') == $field ? 'selected' : '' }}>
                                                Sort by {{ ucwords(str_replace('_', ' ', $field)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <select name="sort_direction" class="form-control"
                                            data-toggle="tooltip" 
                                            title="Choose sort direction">
                                        <option value="asc" {{ request('sort_direction') == 'asc' ? 'selected' : '' }}>
                                            Ascending
                                        </option>
                                        <option value="desc" {{ request('sort_direction') == 'desc' ? 'selected' : '' }}>
                                            Descending
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Field Filters -->
                        <div class="col-md-6">
                            <label>Field Filters</label>
                            @foreach($fillableFields as $field)
                                <div class="form-group">
                                    <select name="filter_{{ $field }}" class="form-control"
                                            data-toggle="tooltip" 
                                            title="Filter by {{ ucwords(str_replace('_', ' ', $field)) }}">
                                        <option value="">Filter {{ ucwords(str_replace('_', ' ', $field)) }}</option>
                                        @foreach($filterOptions[$field] as $option)
                                            @if($field === 'import_id')
                                                @php
                                                    $import = \App\Models\Import::find($option);
                                                    $displayValue = $import ? "Import #{$option} - {$import->file_name}" : $option;
                                                @endphp
                                                <option value="{{ $option }}" 
                                                    {{ request("filter_$field") == $option ? 'selected' : '' }}>
                                                    {{ $displayValue }}
                                                </option>
                                            @else
                                                <option value="{{ $option }}" 
                                                    {{ request("filter_$field") == $option ? 'selected' : '' }}>
                                                    {{ $option }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row">
                        <div class="col-12">
                            <div class="float-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Apply Filters
                                </button>
                                <a href="{{ route('imported-data.orders') }}" class="btn btn-default">
                                    <i class="fas fa-undo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Content -->
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        @foreach($fillableFields as $field)
                            <th>{{ ucwords(str_replace('_', ' ', $field)) }}</th>
                        @endforeach
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            @foreach($fillableFields as $field)
                                <td>
                                    @if(in_array($field, ['total_price', 'cost', 'shipping_cost', 'profit']))
                                        ${{ number_format($order->$field, 2) }}
                                    @else
                                        {{ $order->$field }}
                                    @endif
                                </td>
                            @endforeach
                            <td>
                                <a href="#" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($fillableFields) + 1 }}" class="text-center">No orders found</td>
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

@section('js')
<script>
    $(document).ready(function() {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Initialize select2 with bootstrap4 theme
        $('select').select2({
            theme: 'bootstrap4'
        });

        // Keep drawer open if there are active filters
        if ({{ json_encode(
            request()->hasAny(['search', 'date_from', 'date_to', 'sort_by', 'sort_direction']) || 
            collect(request()->all())->keys()->contains(fn($key) => str_starts_with($key, 'filter_'))
        ) }}) {
            $('#searchDrawer').addClass('show');
        }

        // Persist select2 search terms
        $('.select2-search__field').each(function() {
            $(this).attr('data-toggle', 'tooltip')
                   .attr('title', 'Type to search options');
        });
    });
</script>
@stop 