@extends('adminlte::page')

@section('title', 'Import History')

@section('content_header')
    <h1>Import History</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Import History</h3>
            <div class="card-tools">
                <div class="input-group input-group-sm" style="width: 250px;">
                    <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>User</th>
                        <th>Import Type</th>
                        <th>File Name</th>
                        <th>Status</th>
                        <th>Records Processed</th>
                        <th>Failed Records</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($imports as $import)
                        <tr>
                            <td>{{ $import->created_at->format('Y-m-d H:i:s') }}</td>
                            <td>{{ $import->user->name }}</td>
                            <td>{{ $import->import_type }}</td>
                            <td>{{ $import->original_filename }}</td>
                            <td>
                                <span class="badge badge-{{ $import->status === 'completed' ? 'success' : ($import->status === 'failed' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($import->status) }}
                                </span>
                            </td>
                            <td>{{ $import->records_processed }}</td>
                            <td>{{ $import->failed_records }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info view-logs" data-import-id="{{ $import->id }}">
                                    <i class="fas fa-eye"></i> View Logs
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            <!-- Pagination will be added here -->
        </div>
    </div>

    <!-- Log Modal -->
    <div class="modal fade" id="logModal" tabindex="-1" role="dialog" aria-labelledby="logModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logModalLabel">Import Logs</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="logs-content">
                        <!-- Logs will be populated dynamically -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
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

            // View logs functionality
            $('.view-logs').click(function() {
                // Implement log viewing functionality
            });
        });
    </script>
@stop 