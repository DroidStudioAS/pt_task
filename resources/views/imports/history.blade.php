@extends('adminlte::page')

@section('title', 'Import History')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Import History</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>File Name</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($imports as $import)
                        <tr>
                            <td>{{ $import->created_at->format('Y-m-d H:i:s') }}</td>
                            <td>{{ $import->file_name }}</td>
                            <td>{{ $import->import_type }}</td>
                            <td>
                                <span class="badge badge-{{ $import->status === 'completed' ? 'success' : ($import->status === 'failed' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($import->status) }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info view-logs" data-import-id="{{ $import->id }}">
                                    <i class="fas fa-list"></i> View Logs
                                </button>
                                <a href="{{ route('imports.orders', $import) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View Orders
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Logs Modal -->
    <div class="modal fade" id="logsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Logs</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h6 id="logFileName" class="mb-3"></h6>
                    <pre id="logContent" style="max-height: 400px; overflow-y: auto;"></pre>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
$(document).ready(function() {
    $('.view-logs').click(function() {
        const importId = $(this).data('import-id');
        
        $.ajax({
            url: `/imports/${importId}/logs`,
            method: 'GET',
            success: function(response) {
                $('#logFileName').text('File: ' + response.filename);
                $('#logContent').text(response.logs);
                $('#logsModal').modal('show');
            },
            error: function(xhr) {
                console.error('Error fetching logs:', xhr);
                alert('Error fetching logs. Please try again.');
            }
        });
    });
});
</script>
@stop 